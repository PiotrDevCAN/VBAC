/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/squadsByTribe.js');

class squadsByTribe {

    squads = [];

    constructor() {

    }

    async getSquadsByTribe() {
        // await for API data
        var dataRaw = await APIData.data;
        this.squads = dataRaw;
        return this.squads;
    }
}

const Squads = new squadsByTribe();

export { Squads as default };
