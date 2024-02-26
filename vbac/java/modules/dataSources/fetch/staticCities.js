// fetch request
const staticCities = fetch("ajax/getStaticCities.php").then((response) => response.json());

export default await staticCities;