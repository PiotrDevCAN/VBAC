// fetch request
const staticCountries = fetch("ajax/getStaticCountries.php").then((response) => response.json());

export default await staticCountries;