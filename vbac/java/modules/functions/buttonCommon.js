/**
 *
 */

var buttonCommon = {
	exportOptions: {
		format: {
			body: function (data, row, column, node) {
				//   return data ?  data.replace( /<br\s*\/?>/ig, "\n") : data ;
				return data ? data.replace(/<br\s*\/?>/ig, "\n").replace(/(&nbsp;|<([^>]+)>)/ig, "") : data;
				//    data.replace( /[$,.]/g, '' ) : data.replace(/(&nbsp;|<([^>]+)>)/ig, "");
			}
		}
	}
};

export { buttonCommon as default };