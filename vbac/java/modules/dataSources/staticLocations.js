/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/staticLocations.js');
// let mapper = await cacheBustImport('./modules/select2dataIdValueMapper.js');
let mapper = await cacheBustImport('./modules/select2dataMapper.js');

class staticLocations {

    locations = [];

    constructor() {

    }

    async getLocations() {
        // await for API data
        var dataRaw = await APIData.data;
        var data = mapper(dataRaw);
        this.locations = data;
        return this.locations;
    }
}

const StaticLocations = new staticLocations();

export { StaticLocations as default };
