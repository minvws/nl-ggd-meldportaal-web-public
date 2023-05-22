import {
  parsePubkey,
  decryptValue,
  encryptValue,
  generateKeyPair,
  importKeyPair,
  exportKeyPair,
  toHex,
} from "./encryption";
import { getFormData } from "./util";
import { getLabelFor } from "./labels";

var frontend_key = null;

// Decrypt the stored form data and fill it in the form
const populateForm = async () => {
  const encryptedFormData = sessionStorage.getItem("form_data");
  if (encryptedFormData === null) return;

  // Decrypt the at-rest form data with our frontend key
  const formData = JSON.parse(
    await decryptValue(
      encryptedFormData,
      frontend_key.publicKey,
      frontend_key.privateKey
    )
  );

  for (let [key, value] of Object.entries(formData)) {
    if (!key.startsWith("data[")) {
      continue;
    }

    // This is an array value, so we have to set the value of the corresponding checkbox
    if (key.endsWith("][]")) {
      if (!Array.isArray(value)) {
        value = [value];
      }

      value.forEach((v) => {
        const el = document.getElementById(v);
        if (el) {
          el.checked = true;
        }
      });
      continue;
    }

    // if the key is data[<id>], we need to strip the data[ and ] from the key
    const id = key.substring(5, key.length - 1);
    const el = document.getElementById(id);
    if (el) {
      el.value = formData[key];
    }
  }
};

// Decrypt the stored form backend data and fill it in the table
const populateConfirmationTable = async () => {
  const encryptedFormData = sessionStorage.getItem("form_data");
  if (encryptedFormData === null) return;

  // Decrypt the at-rest form data with our frontend key
  const formData = JSON.parse(
    await decryptValue(
      encryptedFormData,
      frontend_key.publicKey,
      frontend_key.privateKey
    )
  );

  // Find locale from the data-locale attribute on the declaration section
  const locale =
    document.getElementById("declaration-section").dataset.locale || "nl";

  for (let [key, value] of Object.entries(formData)) {
    if (!key.startsWith("data[")) {
      continue;
    }

    // This is an array value, so we have to set the value of the corresponding checkbox
    if (key.endsWith("][]")) {
      const id = key.substring(5, key.length - 3);
      const el = document.getElementById(id);

      if (!Array.isArray(value)) {
        value = [value];
      }

      let s = "<ul>";
      value.forEach((v) => {
        s += "<li>" + getLabelFor(locale, id, v) + "</li>";
      });
      s += "</ul>";

      el.innerHTML = s;
      continue;
    }

    // if the key is data[<id>], we need to strip the data[ and ] from the key
    const id = key.substring(5, key.length - 1);
    const el = document.getElementById(id);
    if (el) {
      el.textContent = getLabelFor(locale, id, formData[key]);
    }
  }
};

// Generate or fetch the frontend key and inject it in the form
const initFrontendKey = async () => {
  if (sessionStorage.getItem("frontend_key") === null) {
    const kp = await generateKeyPair();
    sessionStorage.setItem("frontend_key", exportKeyPair(kp));
    // If there is any form_data, we cannot decrypt it anymore, so we need to clear it
    sessionStorage.removeItem("form_data");
  }

  // Load the frontend key. Note that this key is stored in the session storage, so theoretically
  // it's possible for an attacker to steal it. However, this will only allow to decrypt the form
  // data on the client side, which is not very useful.
  frontend_key = await importKeyPair(sessionStorage.getItem("frontend_key"));
};

export const initConfirmedPage = async () => {
  const id = document.querySelector("#confirmed_page");
  if (!id) return;

  // The current form is submitted correctly, so we can clear the form data on the client side
  sessionStorage.removeItem("form_data");
};

export const initConfirmTable = async () => {
  const table = document.querySelector("#confirm_table");
  if (!table) return;

  await initFrontendKey();
  await populateConfirmationTable();
};

export const initForm = async () => {
  const form = document.querySelector("#contact_form");
  if (!form) return;

  await initFrontendKey();
  await populateForm();
  initEventListeners(form);
  initAddressSearch();
  initDateFields();

  refreshFormFields();
};

const postcodeHandler = async () => {
  const postcode = document.getElementById("postcode").value || "";
  if (postcode.length < 5) return;

  const houseNr = document.getElementById("house_number").value || "";
  if (houseNr === "") return;

  const el = document.getElementsByName("backend_public_key")[0];
  const backendPubKey = parsePubkey(el.value);

  const encryptedPostcode = await encryptValue(postcode, backendPubKey);
  const encryptedHn = await encryptValue(houseNr, backendPubKey);

  // Generate a random key pair for the address search
  const kp = await generateKeyPair();

  const data = {
    pc: encryptedPostcode,
    hn: encryptedHn,
    key: toHex(kp.publicKey),
  };

  fetch(`/address`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  })
    .then((response) =>
      response.json().then((data) => ({ status: response.status, body: data }))
    )
    .then(async (data) => {
      if (data.status === 503) {
        // Service is disabled, don't show an error
        const el = document.getElementById("postcode_notification_message");
        el.classList.add("hidden");

        return;
      }

      if (data.status !== 200) {
        const el = document.getElementById("postcode_notification_message");
        el.classList.remove("hidden");

        return;
      }

      var address = await decryptValue(
        data.body.data,
        kp.publicKey,
        kp.privateKey
      );
      address = JSON.parse(address);
      if (address === null) {
        const el = document.getElementById("postcode_notification_message");
        el.classList.remove("hidden");

        return;
      }

      var el = document.getElementById("postcode_notification_message");
      el.classList.add("hidden");

      // Only fill when street is empty, don't overwrite anything
      el = document.getElementById("street");
      if (el.value === "") {
        document.getElementById("street").value = address.street;
        document.getElementById("city").value = address.city;
        document.getElementById("country").value =
          address.country.toUpperCase();
      }
    });
};

const dateHandler = async (e) => {
  if (e.target.id === "birthdate") {
    checkDate(e.target.value, "birthdate_notification_message", ">", 100);
  }

  if (e.target.id === "date_of_sample_collection") {
    checkDate(
      e.target.value,
      "date_of_sample_collection_notification_message",
      "<",
      365
    );
  }

  if (e.target.id === "date_of_test_result") {
    checkDate(
      e.target.value,
      "date_of_test_result_notification_message",
      "<",
      365
    );
  }
};

const checkDate = (value, notification_message_id, operator, days) => {
  const dt = new Date(value);
  const now = new Date();

  const notificationEl = document.getElementById(notification_message_id);
  if (isNaN(dt)) {
    notificationEl.classList.add("hidden");
  }

  if (operator === "<") {
    if (dt < now - days * 24 * 60 * 60 * 1000) {
      notificationEl.classList.remove("hidden");
    } else {
      notificationEl.classList.add("hidden");
    }
  }
  if (operator === ">") {
    if (dt > now - days * 24 * 60 * 60 * 1000) {
      notificationEl.classList.remove("hidden");
    } else {
      notificationEl.classList.add("hidden");
    }
  }
};

const initDateFields = () => {
  var el = document.getElementById("birthdate");
  if (el) {
    el.addEventListener("change", dateHandler);
  }

  el = document.getElementById("date_of_sample_collection");
  if (el) {
    el.addEventListener("change", dateHandler);
  }

  el = document.getElementById("date_of_test_result");
  if (el) {
    el.addEventListener("change", dateHandler);
  }
};

const initAddressSearch = () => {
  var el = document.getElementById("postcode");
  if (!el) return;
  el.addEventListener("change", postcodeHandler);

  el = document.getElementById("house_number");
  if (!el) return;
  el.addEventListener("change", postcodeHandler);
};

const refreshFormFields = () => {
  let el = document.getElementById("test_after_contact_tracing");
  if (el.value === "yes") {
    document.getElementById("div_bco_number").classList.remove("hidden");
  } else {
    document.getElementById("div_bco_number").classList.add("hidden");
  }

  el = document.getElementById("return_from_trip");
  if (el.value === "yes") {
    document.getElementById("div_country_stay").classList.remove("hidden");
    document.getElementById("div_flight_number").classList.remove("hidden");
  } else {
    document.getElementById("div_country_stay").classList.add("hidden");
    document.getElementById("div_flight_number").classList.add("hidden");
  }

  el = document.getElementById("first_day_of_illness_known");
  if (el.value === "known" || el.value === "estimated") {
    document
      .getElementById("div_first_day_of_illness_date")
      .classList.remove("hidden");
  } else {
    document
      .getElementById("div_first_day_of_illness_date")
      .classList.add("hidden");
  }

  var options1 = [
    "healthcare_worker_or_paramedic_in_hospital",
    "care_worker_or_paramedic_in_nursing_or_care_home",
    "healthcare_worker_or_paramedic_elsewhere_with_close_contact",
  ];
  options1.forEach((name) => {
    el = document.getElementById(name);
    showHideDiv("div_caregiver_type", options1);
  });

  var options2 = ["other_professions_with_close_contact"];
  options2.forEach((name) => {
    el = document.getElementById(name);
    showHideDiv("div_contact_profession", options2);
  });

  el = document.getElementById("patient_gp_client_vvt_or_risk_group");
  if (el.value === "patient_client" || el.value === "risk_group") {
    document.getElementById("div_risk_group").classList.remove("hidden");
  } else {
    document.getElementById("div_risk_group").classList.add("hidden");
  }
};

const showHideDiv = (divId, options) => {
  let checked = 0;
  options.forEach((name) => {
    const el = document.getElementById(name);
    if (el && el.checked) {
      checked++;
    }
  });

  if (checked > 0) {
    document.getElementById(divId).classList.remove("hidden");
  } else {
    document.getElementById(divId).classList.add("hidden");
  }
};

const submitForm = async (event) => {
  event.preventDefault();

  const form = event.target;
  const formData = getFormData(form);
  const strFormData = JSON.stringify(formData);

  // Encrypt and store the current data at the client's session storage
  const encryptedFormDataAtRest = await encryptValue(
    strFormData,
    frontend_key.publicKey
  );
  sessionStorage.setItem("form_data", encryptedFormDataAtRest);

  // Encrypt and submit the current data to the backend
  const pubkey = parsePubkey(formData.backend_public_key);
  const encryptedFormDataForBackend = await encryptValue(strFormData, pubkey);
  submitFormData(form, formData._token, encryptedFormDataForBackend);
};

const submitFormData = (originalForm, csrfToken, encryptedFormData) => {
  // Create a new duplicate form that we will actually submit instead of the "main form".
  const form = document.createElement("form");
  form.method = originalForm.method;
  form.action = originalForm.action;
  form.className = "hidden";
  document.body.appendChild(form);

  let input = document.createElement("input");
  input.name = "_token";
  input.value = csrfToken;
  form.appendChild(input);

  input = document.createElement("input");
  input.name = "formdata";
  input.value = encryptedFormData;
  form.appendChild(input);

  // Submit the encrypted values
  form.submit();
};

const initEventListeners = (form) => {
  form.addEventListener("submit", submitForm);

  form.addEventListener("keydown", (evt) => {
    if (evt.key === "Enter") evt.preventDefault();
  });

  let el = document.getElementById("test_after_contact_tracing");
  el.addEventListener("change", refreshFormFields);

  el = document.getElementById("return_from_trip");
  el.addEventListener("change", refreshFormFields);

  el = document.getElementById("first_day_of_illness_known");
  el.addEventListener("change", refreshFormFields);

  var options1 = [
    "healthcare_worker_or_paramedic_in_hospital",
    "care_worker_or_paramedic_in_nursing_or_care_home",
    "healthcare_worker_or_paramedic_elsewhere_with_close_contact",
  ];
  options1.forEach((name) => {
    el = document.getElementById(name);
    el.addEventListener("change", refreshFormFields);
  });

  var options2 = ["other_professions_with_close_contact"];
  options2.forEach((name) => {
    el = document.getElementById(name);
    el.addEventListener("change", refreshFormFields);
  });

  el = document.getElementById("patient_gp_client_vvt_or_risk_group");
  el.addEventListener("change", refreshFormFields);
};
