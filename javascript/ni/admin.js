var alib = new al();
var DT_REPORT;

$(document).ready(function(){
    alib.CHECK_SESSION();
    alib.LOAD_NAVBAR('topnav','adminnavbar.html'); 
    MOVIE_LOAD();
    alib.LOAD_NAVBAR('footer','footer.html');
    
    $('#topnav').on('click', 'a', function()
    {
        if(this.id == 'alogout')
        {
             alib.DESTROY_SESSION('php/settings/sessiondestroy.php','http://apitinfo.ml/index');
        }
    });
    
    $('#txtname').keyup(function(){
      DT_REPORT.column(0).search($(this).val()).draw() ;
    });
        $('#txtcategory').keyup(function(){
          DT_REPORT.column(2).search($(this).val()).draw() ;
    });

});

function MOVIE_LOAD()
{

    $('#b1').remove();
    $.ajax({
      url:"php/movies/PM_Movies_Get.php",
      method:"POST",
    //   data :{varempid:empid},
      dataType: "JSON",
      
      success:function(data)
      {
        console.log(data);
        DT_REPORT = $('#tbreport').DataTable ({
        "scrollY":        "600px",
        "scrollCollapse": true,
        "paging":         false,
        "data" : data,
        "columns" : [
            { "data" : "MovieName" },
            { "data" : "MovieDescription" },
            { "data" : "CategoryName" },
            { "data" : "MovieViews" },
            ],
            dom: 'Bfrtip',
            bDestroy: true,
            ordering:false,
          buttons: [
                    {
                       extend: 'csv',
                        messageTop: function () {
                        var title = document.getElementById("txttitle").value;
                        return '<h2>'+title+'</h1><br>';
                        },
                        title: '',
                       exportOptions: { columns: [0,2,3] }
                     },
                    {
                        extend: 'print',
                        messageTop: function () {
                        var title = document.getElementById("txttitle").value;
                        return '<h2>'+title+'</h1><br>';
                        },
                        title: '',
                        exportOptions: { columns: [0,2,3] }
                     },
                    {
                        extend: 'excel',     
                        messageTop: function () {
                        var title = document.getElementById("txttitle").value;
                        return '<h2>'+title+'</h1><br>';
                        },
                        title: '',
                        exportOptions: { columns: [0,2,3] }
                     },
                     {
                        extend: 'pdf',
                        messageTop: function () {
                        var title = document.getElementById("txttitle").value;
                        return '<h2>'+title+'</h1><br>';
                        },
                        title: '',
                        exportOptions: { columns: [0,1,3] }
                     },
                 ],
          "columnDefs": [
            //   align right number columns
                { targets: -1,
        className: 'dt-body-right'},
                { "width": "25%", "targets": 0 },
                { "width": "10%", "targets": 2 },
                { "width": "10%", "targets": 3 }
                ]
             });
        },
        error: function(data) 
         {
            alert('error!');
         }
    });
}
