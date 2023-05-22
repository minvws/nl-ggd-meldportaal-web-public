import elementsEn from "../elements-en.json";
import elementsNl from "../elements-nl.json";

const elements = {
  en: elementsEn, // English labels
  nl: elementsNl, // Dutch labels
};

// Retrieves a label for a given language/locale and key. If the key is not found, the value is returned as-is.
export const getLabelFor = (lang, key, value) => {
  if (elements[lang][key] === undefined) {
    return value;
  }

  return elements[lang][key][value] || value;
};
