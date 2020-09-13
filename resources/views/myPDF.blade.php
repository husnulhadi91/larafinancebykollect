<!DOCTYPE html>
<html>
<head>
	<title>Hi</title>
</head>
<body>
	<table>
		<tr>
			<td>
				{{$company->name}}
			</td>
		</tr>
		<tr>
			<td>
				Application Information<br>
				<table>
					<tr>
						<td>
							{{$user->name}}
						</td>
						<td>
							{{$user->email}}
						</td>
						<td>
							{{$user->address}}
						</td>
					</tr>
				</table>
				Financing Information<br>
				<table>
					<tr>
						<td>
							{{$financed->amount}}
						</td>
						<td>
							{{$financed->tenure}} days
						</td>
						<td>
							{{$financed->created_at}}
						</td>
					</tr>
				</table>
		</tr>
		<tr>
			<td>
				Payment Info<br>
				<table>
					@foreach($payment as $p)
					<tr>
						<td>
							{{$p->amount}}
						</td>
						<td>
							@if($p->status == 0) 
							Failed
							@else
							Success
							@endif
						</td>
						<td>
							{{$financed->created_at}}
						</td>
					</tr>
					@endforeach
				</table>
			</td>
		</tr>
	</table>
</body>
</html>