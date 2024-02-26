/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/staticAgileSquads.js');
let mapper = await cacheBustImport('./modules/select2dataASMapper.js');

class staticAgileSquads {

    squads = [];

    constructor() {

    }

    async getSquads() {
        // await for API data
        var dataRaw = await APIData.data;
        var data = mapper(dataRaw);
        this.squads = data;
        return this.squads;
    }
}

const StaticAgileSquads = new staticAgileSquads();

export { StaticAgileSquads as default };
