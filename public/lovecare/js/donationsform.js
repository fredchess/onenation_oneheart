const name = document.getElementById('name')
const email = document.getElementById('email')
const amount = document.getElementById('amount')
const form = document.getElementById('donation_form')
const btn = document.getElementById('donateBtn')

let phoneInputField = document.querySelector("#phone");
let phoneInput = window.intlTelInput(phoneInputField, {
    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
    onlyCountries: ["CM"],
});
//
// form.addEventListener('submit', (e) => {
//     if (!phoneInput.isValidNumber()) {
//         phoneInputField.setCustomValidity("Numero incorrect")
//         phoneInputField.reportValidity(false)
//         return false
//         e.preventDefault()
//     }
//     if (form.checkValidity()) {
//         return true
//     }
//     console.log('il ya des invalides')
//     const invalids = document.querySelectorAll(':invalid')
//
//     for (var item of invalids){
//         if (item.style.display !== 'none') return false
//     }
//     return true
//     e.preventDefault()
// })
