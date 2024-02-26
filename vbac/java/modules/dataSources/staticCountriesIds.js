/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/staticCountries.js');
let mapper = await cacheBustImport('./modules/select2dataIdValueMapper.js');

class staticCountriesIds {

    countries = [];

    constructor() {

    }

    async getCountries() {
        // await for API data
        var dataRaw = await APIData.data;
        var data = mapper(dataRaw);
        this.countries = data;
        return this.countries;
    }
}

const StaticCountriesIds = new staticCountriesIds();

export { StaticCountriesIds as default };
