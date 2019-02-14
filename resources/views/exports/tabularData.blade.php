@forelse($data as $table_data)
<table border="1">
    <thead>
      @if(isset($table_data['table_titles'])&& !empty($table_data['table_titles']))
     @foreach($table_data['table_titles'] as $key=>$value)
    <tr>
    <th colspan="{{count($table_data['columns'])}}">{{$value}}</th>
    </tr>
    @endforeach
    @endif
     <tr>
        <th>Sr.NO</th>
        <th>User Name</th>
        @if(isset($table_data['label_heading']) && !empty($table_data['label_heading']))
        <th>{{$table_data['label_heading']}}</th>
        @endif
        @foreach($table_data['columns'] as $column)
           @if(!in_array($column,array('id','row_label'))) 
              <th>{{ ucwords(str_replace('_', ' ', strtolower($column))) }}</th> 
           
           @endif
     @endforeach
     <th>Status</th>
     <th>Submitted Date</th>
       </tr>
   </thead>
    <tbody>
        @foreach($table_data['table_data'] as $key=>$value)

         <tr> 
          <td>{{$key+1}}</td>
          <td>{{$value->first_name.' '.$value->last_name}}</td>
          @if(isset($value->row_label))
          <td>{{$value->row_label}}</td>
          @endif
           @foreach($table_data['columns'] as $column)
             @if(!in_array($column,array('id','row_label')))
            <td>
                  {{ ucwords($value->$column)}} 
            </td>
            @endif
          @endforeach
          <td>{{ $value->status}}</td>
          <td>{{ $value->created_at}}</td>
           </tr>
         @endforeach
    </tbody>
</table>
@empty
@endforelse

