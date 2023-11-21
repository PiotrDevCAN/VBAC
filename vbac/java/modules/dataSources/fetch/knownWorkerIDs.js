// fetch request
const knownWorkerIDs = fetch("ajax/getKnownWorkerIDs.php").then((response) => response.json());

export default await knownWorkerIDs;