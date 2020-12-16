<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">用户ID：{{ $user->id }}</h3>
        <div class="box-tools">
            <div class="btn-group pull-right" style="margin-right: 10px">
                <a href="{{ route('admin.user.index') }}" class="btn btn-sm btn-default"><i class="fa fa-list"></i> 列表</a>
            </div>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-bordered" style="text-align: center">
            <tbody>
            <tr>
                <td>姓名：</td>
                <td>{{ $user->name }}</td>
                <td>手机号：</td>
                <td>{{ $user->mobile }}</td>

            </tr>
            <tr>
                <td>邮箱：</td>
                <td>{{ $user->email }}</td>
                <td>性别：</td>
                <td>{{ $user->gender }}</td>
            </tr>

            <tr>
                <td>最近登陆：</td>
                <td>{{ $user->last_login_at  }}</td>
                <td>创建时间：</td>
                <td>{{ $user->created_at  }}</td>

            </tr>
            <tr>
                <td>头像：</td>
                <td><img src="{{ $user->avatar }}" style="width: 100px;height:100px"></td>
                <td>微信头像：</td>
                <td><img src="{{ $user->weixin_avatar }}" style="width: 100px;height:100px"></td>
            </tr>
            <tr>
                <td>简介：</td>
                <td colspan="3">{{ $user->introduction }}</td>
            </tr>
            @if($user->country)
            <tr>
                <td>地址：</td>
                <td colspan="3"> {{ $user->country }} - {{ $user->province }} - {{ $user->city }}</td>
            </tr>
            @endif
            </tbody>
        </table>
    </div>
</div>

