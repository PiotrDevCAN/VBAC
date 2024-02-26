/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/staticCountries.js');
// let mapper = await cacheBustImport('./modules/select2dataIdValueMapper.js');
let mapper = await cacheBustImport('./modules/select2dataMapper.js');

class staticCountries {

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

const StaticCountries = new staticCountries();

export { StaticCountries as default };
