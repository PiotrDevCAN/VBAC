// fetch request
const staticAgileTribes = fetch("ajax/getStaticAgileTribes.php").then((response) => response.json());

export default await staticAgileTribes;