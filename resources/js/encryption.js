// TextDecoder polyfill
import "fast-text-encoding";
import sodium from "libsodium-wrappers";
import base64 from "base-64";

export const exportKeyPair = (keyPair) => {
  return base64.encode(
    JSON.stringify({
      publicKey: uint8ArrayToBase64(keyPair.publicKey),
      privateKey: uint8ArrayToBase64(keyPair.privateKey),
      keyType: "x25519",
    })
  );
};

export const importKeyPair = async (keyPair) => {
  return JSON.parse(base64.decode(keyPair), (key, value) => {
    if (key === "publicKey" || key === "privateKey") {
      return base64ToUint8Array(value);
    }
    return value;
  });
  /*
  const kp = await generateKeyPair();

  const data = JSON.parse(base64.decode(keyPair));
  kp.privateKey = base64ToUint8Array(data.privateKey);
  kp.publicKey = base64ToUint8Array(data.publicKey);

  return kp;
 */
};

export const parsePubkey = (data) => {
  return base64ToUint8Array(data);
};

export const encryptValue = async (value, pubkey) => {
  await sodium.ready;
  const result = await sodium.crypto_box_seal(value, pubkey);
  return uint8ArrayToBase64(result);
};

const utf8Decoder = new TextDecoder("utf-8");

export const decryptValue = async (value, pubkey, privatekey) => {
  await sodium.ready;
  return utf8Decoder.decode(
    await sodium.crypto_box_seal_open(
      base64ToUint8Array(value),
      pubkey,
      privatekey
    )
  );
};

export const generateKeyPair = async () => {
  await sodium.ready;
  return await sodium.crypto_box_keypair();
};

export const toHex = (value) => {
  return sodium.to_hex(value);
};

const base64ToUint8Array = (input) =>
  Uint8Array.from(base64.decode(input), (c) => c.charCodeAt(0));

const uint8ArrayToBase64 = (input) =>
  base64.encode(
    input.reduce((acc, char) => acc + String.fromCharCode(char), "")
  );
