<script type="text/javascript">
  $(document).ready(function(){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#inputProvince').change(function(){
      const province_id = $(this).val();
      $.ajax({
          type: 'post',
          url: '{{ route("regency.index") }}',
          data: {
            province_id : province_id
          },
          dataType: 'json',
          success: function (data) {
            $('#inputDistrict').html('');
            $('#inputVillage').html('');
            let option = '';
            option += `
              <option value="">== Pilih ==</option>
            `
            data.forEach(function(d,index){
              option += `
              <option value="${d.id}">${d.name}</option>
              `
            });
            $('#inputRegency').html(option);
          },
          error: function (data) {
              console.log(data);
          }
      });
    })

    $('#inputRegency').change(function(){
      const regency_id = $(this).val();
      console.log(regency_id);
      $.ajax({
          type: 'post',
          url: '{{ route("district.index") }}',
          data: {
            regency_id : regency_id
          },
          dataType: 'json',
          success: function (data) {
            $('#inputVillage').html('');
            let option = '';
            option += `
              <option value="">== Pilih ==</option>
            `
            data.forEach(function(d,index){
              option += `
              <option value="${d.id}">${d.name}</option>
              `
            });
            $('#inputDistrict').html(option);
          },
          error: function (data) {
              console.log(data);
          }
      });
    })

    $('#inputDistrict').change(function(){
      const district_id = $(this).val();
      $.ajax({
          type: 'post',
          url: '{{ route("village.index") }}',
          data: {
            district_id : district_id
          },
          dataType: 'json',
          success: function (data) {
            let option = '';
            option += `
              <option value="">== Pilih ==</option>
            `
            data.forEach(function(d,index){
              option += `
              <option value="${d.id}">${d.name}</option>
              `
            });
            $('#inputVillage').html(option);
          },
          error: function (data) {
              console.log(data);
          }
      });
    })
  })
</script>