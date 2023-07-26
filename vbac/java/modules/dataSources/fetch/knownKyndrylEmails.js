// fetch request
const knownKyndrylEmails = fetch("ajax/getKnownKyndrylEmails.php").then((response) => response.json());

export default await knownKyndrylEmails;