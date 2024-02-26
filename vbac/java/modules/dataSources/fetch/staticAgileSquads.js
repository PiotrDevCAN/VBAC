// fetch request
const staticAgileSquads = fetch("ajax/getStaticAgileSquads.php").then((response) => response.json());

export default await staticAgileSquads;