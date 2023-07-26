// fetch request
const knownExternalEmails = fetch("ajax/getKnownExternalEmails.php").then((response) => response.json());

export default await knownExternalEmails;