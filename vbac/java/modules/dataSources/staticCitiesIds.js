/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/staticCities.js');
let mapper = await cacheBustImport('./modules/select2dataIdValueMapper.js');

class staticCitiesIds {

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

const StaticCitiesIds = new staticCitiesIds();

export { StaticCitiesIds as default };
