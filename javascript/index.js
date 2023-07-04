var CATEGORYID;
var ROW_DATA;
var alib;
var DT_CATEGORY;

$(document).ready(function(){
    INDEX_LOAD();
    $("#btnadd").click(function() { CLEAR_DATA(); });
});

$(document).on("click", "a" , function() {
    var divid = '#'+$(this).data("child");
    if ( $(divid).is(':visible') ){
        $(divid).hide();
    }else{
        $(divid).show();
    }
});

function INDEX_LOAD()
{
    $.ajax({
      url:"php/AdminController.php",
      method:"POST",
      data :{action:'index'},
      dataType: "JSON",
      success:function(data)
      {
        console.log(data);
        DT_CATEGORY = $('#tbcategory').DataTable ({
             "autoWidth": false,
        "data" : data,
        "columns" : [
            { "data" : "adBarcode" },
            { "data" : "adDescription" },
            { "data" : "adDate" },
            { "data" : "adGrouping" },
            { "data" : "adSOH" },
            { "data" : "adSO" },
            { "data" : "adReceving" },
            { "data" : "adIA" },
            { "data" : "adSA" },
            { "data" : "adWA" },
            { "data" : "adVariance" },

            {
              targets: -1,
              data: null,
              render: function ( data, type, row ) {
                    retdata = "<button type='button' id = "+ data.adBarcode +" class='btn btn-info'>Ledger</button>";
                return retdata;
              }
            }
            ],
        "columnDefs": [
            { targets: 4, className: 'dt-body-right' },
            { targets: 5, className: 'dt-body-right' },
            { targets: 6, className: 'dt-body-right' },
            { targets: 7, className: 'dt-body-right' },
            { targets: 8, className: 'dt-body-right' },
            { targets: 9, className: 'dt-body-right' },
            { targets: 10, className: 'dt-body-right' },
            { "width": "100px", "targets": 1 },
            { "width": "80px", "targets": 2 },
            { "width": "40px", "targets": 4 },
            { "width": "60px", "targets": 2 },
            { "width": "60px", "targets": 6 },
            { "width": "30px", "targets": 8 },
            { "width": "30px", "targets": 9 },
            ]
             });
        
         //DataTable click event
         $('#tbcategory tbody').on( 'click', 'button', function () {

            console.log(ROW_DATA);

            var table1 = document.getElementById("table1");
            var table2 = document.getElementById("table2");

            if(this.id === '1090100213799'){
                table1.style.display = "block";
                table2.style.display = "none";
                $('li > a[href="#menu1"]').tab("show");

            }
            else if(this.id === '1090100024814'){
                table1.style.display = "none";
                table2.style.display = "block";
                $('li > a[href="#menu1"]').tab("show");
            }
            else{
                console.log('error!');
                  Swal.fire({
                      position: 'center',
                      icon: 'error',
                      title: 'This product doesnt have record',
                      showConfirmButton: false,
                      timer: 1500
                    })
            }
        } 
        );
        
        $('#tbcategory tbody').on( 'click', 'tr', function () { 
            ROW_DATA = this;} );
        },
        error: function(data) 
         {
            // alert('error!');
            console.log(data);
         }
    });
}