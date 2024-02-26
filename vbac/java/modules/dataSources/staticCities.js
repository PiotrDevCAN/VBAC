/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/staticCities.js');
// let mapper = await cacheBustImport('./modules/select2dataIdValueMapper.js');
let mapper = await cacheBustImport('./modules/select2dataMapper.js');

class staticCities {

    cities = [];

    constructor() {

    }

    async getCities() {
        // await for API data
        var dataRaw = await APIData.data;
        var data = mapper(dataRaw);
        this.cities = data;
        return this.cities;
    }
}

const StaticCities = new staticCities();

export { StaticCities as default };
