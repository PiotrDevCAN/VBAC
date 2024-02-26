// fetch request
const squads = fetch("ajax/getSquadsByTribe.php").then((response) => response.json());

export default await squads;