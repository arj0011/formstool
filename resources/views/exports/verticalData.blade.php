<table>
    <thead>
    <tr>
        <th>#</th>
         @foreach($data['columns'] as $key => $value)
             <th>{{ucwords(str_replace('_' , ' ' ,$value))}}</th>
         @endforeach
    </tr>
    </thead>
    <tbody>
        <tr>
            <td>1</td>
            @foreach($data['data'] as $key1 => $value1)
               @foreach($data['columns'] as $key => $value)
                 @if($value != 'id')
                   <td>{{$value1->$value}}</td>
                 @endif
               @endforeach
            @endforeach
        </tr>
    </tbody>
</table>