import './bootstrap';
// import 'bootstrap/dist/css/bootstrap.min.css';
// show month on report page
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css"; // Import Flatpickr CSS
import monthSelectPlugin from "flatpickr/dist/plugins/monthSelect";
import "flatpickr/dist/plugins/monthSelect/style.css";


document.addEventListener("DOMContentLoaded", function () {
    flatpickr("#monthPicker", {
      plugins: [
        new monthSelectPlugin({
          shorthand: true,      // Optional: shows abbreviated month names
          dateFormat: "Y-m",    // Format for input value, e.g., "2024-11"
          altFormat: "F Y"      // Display format, e.g., "November 2024"
        })
      ]
    });

    // flatpickr("#yearPicker", {
    //     dateFormat: "Y", // Year only
    //     defaultDate: new Date().getFullYear().toString() // Optional default
    //   });
  });
