import { onDomReady } from "./util";
import { initConfirmedPage, initConfirmTable, initForm } from "./form";

onDomReady(() => {
  initForm();
  initConfirmTable();
  initConfirmedPage();
});
