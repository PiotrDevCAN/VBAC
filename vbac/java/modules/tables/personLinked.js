/*
 *
 *
 *
 */

let person = await cacheBustImport('./modules/tables/person.js');

class personLinked extends person {

  constructor(preBoardersAction) {
    super(preBoardersAction);
  }
}

export { personLinked as default };