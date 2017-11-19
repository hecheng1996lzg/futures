@extends('layouts.layout1')
@section('content')
    <article class="list-option">
        @if(session('update_log'))
            <section class="msg-info list-add form-tab list-option-show">
                    @foreach(session('update_log') as $value)
                        <h2>
                            <span>{{ $value['name'] }}</span> &nbsp;
                            <span>{{ $value['msg'] }}</span> &nbsp;
                            <span>{{ $value['end'] or '' }}</span>
                        </h2>
                    @endforeach
            </section>
        @endif
        <form id="addForm"  method="post" enctype="multipart/form-data" action="{{ asset('variety/update/all') }}">
            <section class="list-add form-tab list-option-show">
                <h2>
                    <span>{{ $variety or '' }}一键更新所有项目数据</span>
                    <div class="container">
                        <div class="bg_con">
                            <input id="checked_1" type="checkbox" class="switch" />
                            <label for="checked_1"></label>
                        </div>
                    </div>
                </h2>
                <div class="list-add-content">
                    <section>
                        <div>
                            <ul>
                                <li>1. 更新文件请手动放入根目录txt_resource文件下（futures\txt_resource）</li>
                                <li>2. 只更新以存在的文件</li>
                                <li>2. 上传过程中关闭，可能会导致未知bug</li>

{{--
                                2017-03-29 3217.00 3128.00 3222.00 3127.00 5442506 3498752
--}}
                            </ul>
                        </div>
                    </section>
                </div>
                <div class="list-add-btn form-btn">
                    <button id="addFormSubmit" class="btn btn-primary btn-lg ripple">提交</button>
                    <button id="removeFormSubmit" class="btn btn-primary btn-lg ripple">关闭</button>
                </div>
            </section>
        </form>
    </article>
@stop