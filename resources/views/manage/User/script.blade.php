{{-- DataTable Function --}}
<script>
    var table = $('#datatable').DataTable({

        bLengthChange: false,

        searching: true,

        responsive: true,

        'processing': true,

        serverSide: true,

        order: [[0, 'desc']],

        "language": {
            "search": "بحث :"
        },

        ajax: "{{ route('User.view') }}",

        columns:
            [
                {data: 'checkBox', name: 'checkBox'},
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'image', name: 'image'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
    });
</script>



{{--Delete --}}
<script>
    var id_num='';
    var checkNum='';
    function deleteFunction(id,check){
      
      id_num=id;
      checkNum=check;
      
      if(check == 2){
        if(checkArray.length == 0){
          alert("{{trans('main.noItemSelected')}}")
          }else{
            $('#DeleteModel').modal();
          }
        }
      
      else{
        $('#DeleteModel').modal();
      }
      
    }

    function yesDelete()
    {
      
      if(checkNum == 1){
      deleteProccess("/manage/User/delete/" +id_num);
      }else{
        
      deleteProccess("/manage/User/delete/"+checkArray +'?type=2');

    }
      }
</script>


<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>


<script>
    function ChangeStatus(status,id) {
        TosetV2('{{ trans("main.proccess") }}','info','',false);
        $.ajax({
            url : '/manage/User/ChangeStatus/' +id +'?status='+status,
            type : 'get',
            success : function(data){
                $.toast().reset('all');
                table.ajax.reload();
                TosetV2('تمت العملية بنجاح','success','',5000);
            }
        })
    }
</script>