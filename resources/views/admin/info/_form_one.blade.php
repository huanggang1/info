<div class="item">
    <label class="lable"  for="tag" >姓   名：</label><input type="text" name="name"  value="{{$infoData['name']}}"  {{$readonly}} autofocus>
    <label class="lable"  for="tag" >性   别：</label>
    <input type="radio"  name="sex"  value="0" @if ($infoData['sex']== 0) checked @endif  {{$readonly}} autofocus>男
    <input type="radio"  name="sex"  value="1" @if ($infoData['sex']== 1) checked @endif {{$readonly}} autofocus>女
    <label class="lable"  for="tag" >民   族：</label><input type="text"  name="nation"  value="{{$infoData['nation']}}" {{$readonly}} autofocus>
    <label class="lable" for="tag" >政治面貌：</label><input type="text"  name="politicalStatus"  value="{{$infoData['politicalStatus']}}"  {{$readonly}} autofocus>
</div>
<div class="item">
    <label class="lable" for="tag" >身份证号：</label><input type="text" name="identityNum"  value="{{$infoData['identityNum']}}"  {{$readonly}} autofocus>
    <label class="lable" for="tag" >工作单位：</label><input type="text"  name="workUnit"  value="{{$infoData['workUnit']}}" {{$readonly}} autofocus>
    <label class="lable" for="tag" >是否加分：</label>
    <input type="radio"  name="addPoints"  value="0" @if ($infoData['addPoints']== 0) checked @endif  {{$readonly}} autofocus>否
    <input type="radio"  name="addPoints"  value="1" @if ($infoData['addPoints']== 1) checked @endif {{$readonly}} autofocus>是
    <label class="lable" for="tag" >手 机号：</label><input type="text" name="phone"  value="{{$infoData['phone']}}" {{$readonly}} autofocus>
</div>
<div class="item">
    
    <label class="lable" for="tag" >备用电话：</label><input type="text"  name="remarksPhone"  value="{{$infoData['remarksPhone']}}" {{$readonly}} autofocus>
    <label class="lable" for="tag" >预留字段：</label><input type="text"  name="reservedFields"  value="{{$infoData['reservedFields']}}" {{$readonly}} autofocus>
</div>
