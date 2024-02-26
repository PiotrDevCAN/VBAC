/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/staticLocations.js');
let mapper = await cacheBustImport('./modules/select2dataLocationMapper.js');

class staticLocationsIds {

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

const StaticLocationsIds = new staticLocationsIds();

export { StaticLocationsIds as default };
