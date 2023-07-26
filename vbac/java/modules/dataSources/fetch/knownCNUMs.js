// fetch request
const knownCNUMs = fetch("ajax/getKnownCNUMs.php").then((response) => response.json());

export default await knownCNUMs;