import flatpickr from "flatpickr";

flatpickr("#appointment_date", {
  enableTime: false,
  dateFormat: "Y-m-d",
  minDate: "today"
});
