/**
 *
 */

let knownKyndrylEmailsFetch = await cacheBustImport('./modules/dataSources/fetch/knownKyndrylEmails.js');

let APIData = knownKyndrylEmailsFetch;

class knownKyndrylEmails {

    emails = [];

    constructor() {

    }

    async getEmails() {
        // await for API data
        var dataRaw = await APIData.data;
        this.emails = Object.values(dataRaw);
        return this.emails;
    }
}

const KnownKyndrylEmails = new knownKyndrylEmails();

export { KnownKyndrylEmails as default };