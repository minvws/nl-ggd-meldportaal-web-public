module.exports = {
  collectCoverage: true,
  coverageDirectory: "node_modules/.coverage",
  coverageProvider: "v8",
  coverageReporters: ["json-summary", "text", "clover"],
  testEnvironment: "jsdom",
};
