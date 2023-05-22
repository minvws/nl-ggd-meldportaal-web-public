import { parsePubkey, encryptValue, encryptedFields } from "./encryption";

// base64-encoded X25519 keypair
const dummyPubkey = "9Q6DGh27j5Zin21i0lPGEmSCIoDNb9Qpn4Md418YJmw=";

describe("parsePubkey", () => {
  it("throws if string input invalid", () => {
    expect(() => parsePubkey("kaas")).toThrow("Invalid pubkey");
  });

  it("returns parsed pubkey", () => {
    const result = parsePubkey(dummyPubkey);
    expect(result.keyType).toBe("x25519");
    expect(result.publicKey).toBe(true);
  });
});

describe("encryptValue", () => {
  it("rejects on invalid pubkey", async () => {
    await expect(encryptValue("kaas", undefined)).rejects.toThrow(
      "Invalid pubkey"
    );
  });

  it("returns a base64 string", async () => {
    const key = parsePubkey(dummyPubkey);
    const result = await encryptValue("kaas", key);
    expect(typeof result).toBe("string");
    // TODO: actually test against dummyPrivkey?
  });
});

// TODO: refactor to test by encrypting a record and checking whether the field
// was encrypted or left untouched
describe("encryptedFields", () => {
  it("fields are correct", () => {
    expect(encryptedFields).toStrictEqual(["foo", "bar"]);
  });
});
