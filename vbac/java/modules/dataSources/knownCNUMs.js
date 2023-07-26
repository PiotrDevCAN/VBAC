/**
 *
 */

let knownCNUMsFetch = await cacheBustImport('./modules/dataSources/fetch/knownCNUMs.js');

let APIData = knownCNUMsFetch;

class knownCNUMs {

    cnums = [];

    constructor() {

    }

    async getCNUMs() {
        // await for API data
        var dataRaw = await APIData.data;
        this.cnums = Object.values(dataRaw);
        return this.cnums;
    }
}

const KnownCNUMs = new knownCNUMs();

export { KnownCNUMs as default };