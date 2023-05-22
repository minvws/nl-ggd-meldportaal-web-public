// NB: this is a *partial* polyfill, tailor-made to libsodium-wrappers.
export const randomBytes = (n) =>
  window.crypto.getRandomValues(new Uint32Array(n));
