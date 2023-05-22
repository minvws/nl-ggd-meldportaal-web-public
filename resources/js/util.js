/**
 * Run the given callback function once after the DOM is ready.
 * @param {() => void} fn
 */
export const onDomReady = (fn) => {
  if (document.readyState !== "loading") return fn();
  document.addEventListener("DOMContentLoaded", () => fn());
};

export const getFormData = (formElement) => {
  const data = {};

  const fd = new FormData(formElement);
  for (const [key] of fd.entries()) {
    // It's possible for checkboxes to have multiple values, so when this happens, store as an array, otherwise store as a regular string
    const v = fd.getAll(key);
    if (Array.isArray(v) && v.length === 1) {
      data[key] = v[0];
    } else {
      data[key] = v;
    }
  }

  return data;
};
