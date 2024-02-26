// fetch request
const staticLocations = fetch("ajax/getStaticLocations.php").then((response) => response.json());

export default await staticLocations;