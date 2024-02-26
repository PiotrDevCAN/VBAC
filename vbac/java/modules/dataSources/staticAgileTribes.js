/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/staticAgileTribes.js');
let mapper = await cacheBustImport('./modules/select2dataATMapper.js');

class staticAgileTribes {

    tribes = [];

    constructor() {

    }

    async getTribes() {
        // await for API data
        var dataRaw = await APIData.data;
        var data = mapper(dataRaw);
        this.tribes = data;
        return this.tribes;
    }
}

const StaticAgileTribes = new staticAgileTribes();

export { StaticAgileTribes as default };
