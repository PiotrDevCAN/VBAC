// fetch request
const knownIBMEmails = fetch("ajax/getKnownIBMEmails.php").then((response) => response.json());

export default await knownIBMEmails;