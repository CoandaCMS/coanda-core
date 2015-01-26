<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns="http://www.w3.org/1999/xhtml" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Daily Digest</title>
</head>

<body bgcolor="#f6f6f6" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; margin: 0; padding: 0;">

<table style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; width: 100%; margin: 0; padding: 20px;">
	<tr style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;">
		<td style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;"></td>
		<td bgcolor="#FFFFFF" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto; padding: 20px; border: 1px solid #f0f0f0;">

			<div style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; max-width: 600px; display: block; margin: 0 auto; padding: 0;">

				<table style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; width: 100%; margin: 0; padding: 0;">
					<tr style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;">
						<td style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;">

							<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">Hi,</p>
							<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">Below is a summary of activity on the site.</p>

							<table width="100%">
								<tr>
									<td width="25%" align="center" style="background: #f6f6f6; font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 10px 0; font-size: 24px; font-weight: bold;">{{ $summary_figures['yesterday'] }}</td>
									<td width="25%" align="center" style="background: #f6f6f6; font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 10px 0; font-size: 24px; font-weight: bold;">{{ $summary_figures['week'] }}</td>
									<td width="25%" align="center" style="background: #f6f6f6; font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 10px 0; font-size: 24px; font-weight: bold;">{{ $summary_figures['month'] }}</td>
									<td width="25%" align="center" style="background: #f6f6f6; font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 10px 0; font-size: 24px; font-weight: bold;">{{ $summary_figures['year'] }}</td>
								</tr>
								<tr>
									<td align="center">Yesterday</td>
									<td align="center">This week</td>
									<td align="center">This month</td>
									<td align="center">This year</td>
								</tr>
							</table>

							@if ($history_list->count() > 0)
                                <h2 style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 10px 0 10px 0; padding: 0; font-size: 16px; font-weight: bold;">Recent activity</h2>

								<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-top: 1px solid #ccc;">
									@foreach ($history_list as $history)
										<tr>
											<td valign="top" class="tight" style="border-bottom: 1px solid #ccc; font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; line-height: 1.6; margin: 0; padding: 5px; font-size: 12px;">
												<span style="white-space: nowrap;">{{ $history->created_at->format(\Config::get('coanda::coanda.datetime_format')) }}</span>
												<br>
												<small>{{ $history->user->full_name }}</small>
											</td>
											<td valign="top" style="border-bottom: 1px solid #ccc; font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; line-height: 1.6; margin: 0; padding: 5px; font-size: 12px;">{{ $history->display_text }}</td>
										</tr>
									@endforeach
								</table>

								@if ($history_list->getTotal() > 50)
									<p>Total <strong>{{ number_format($history_list->getTotal()) }}</strong>, <a href="{{ Coanda::adminUrl('history') . '?from=' . $from->format('d/m/Y') . '&to=' . $to->format('d/m/Y') }}">view all online</a></p>
								@endif
							@endif

						</td>
					</tr>
				</table>

			</div>

		</td>
		<td style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;"></td>
	</tr>
</table>
</body>
</html>