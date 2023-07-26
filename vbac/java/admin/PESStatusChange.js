/*
 *
 *
 *
 */

class PESStatusChange {
    
    constructor() {
        $('#pesStatus').DataTable({
            orderCellsTop: true,
            autoWidth: true,
            responsive: true,
            processing: true,
            dom: 'Blfrtip',
            buttons: [
                'colvis',
                'excelHtml5'
            ],
            order: [[1, "asc"]],
        });
    }
}

const PesStatusChange = new PESStatusChange();

export { PesStatusChange as default };