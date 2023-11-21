/**
 *
 */

let knownWorkerIDsFetch = await cacheBustImport('./modules/dataSources/fetch/knownCNUMs.js');

let APIData = knownWorkerIDsFetch;

class knownWorkerIDs {

    workerIDs = [];

    constructor() {

    }

    async getWorkerIDs() {
        // await for API data
        var dataRaw = await APIData.data;
        this.workerIDs = Object.values(dataRaw);
        return this.workerIDs;
    }
}

const KnownWorkerIDs = new knownWorkerIDs();

export { KnownWorkerIDs as default };