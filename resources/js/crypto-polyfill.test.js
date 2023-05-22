import { randomBytes } from "./crypto-polyfill";

// Not implemented in jsdom :')
window.crypto = { getRandomValues: jest.fn((array) => array) };

describe("randomBytes", () => {
  it("should call crypto.getRandomValues with a Uint32Array of the request length", () => {
    const result = randomBytes(8);
    expect(window.crypto.getRandomValues).toHaveBeenCalledTimes(1);
    expect(window.crypto.getRandomValues).toHaveBeenCalledWith(
      expect.any(Uint32Array)
    );
    expect(result).toBeInstanceOf(Uint32Array);
    expect(result).toHaveLength(8);
  });
});
