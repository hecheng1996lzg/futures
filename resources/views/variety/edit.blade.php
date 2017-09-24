@extends('layouts.layout1')
@section('content')
    <article class="list-option">
        <form id="addForm"  method="post" enctype="multipart/form-data" action="{{ asset('variety/add') }}">
            <section class="list-add form-tab list-option-show">
                <h2>
                    <span>{{ $variety or '' }}文件更新</span>
                    <div class="container">
                        <div class="bg_con">
                            <input id="checked_1" type="checkbox" class="switch" />
                            <label for="checked_1"></label>
                        </div>
                    </div>
                </h2>
                <div class="list-add-content content">
                    <section>
                        <div class="w100">
                            <label>请选择对应上传文件，可以拖入：{{ $errors->first('fileText') }}</label>
                            <p>
                                <input name="fileText" type="file" class="input-text" placeholder="请选择上传文件，可以拖入">
                            </p>
                        </div>
                    </section>
                    <section>
                        <div>
                            <ul>
                                <li>1. 请上传对应文件，防止数据混乱</li>
                                <li>2. 上传过程中关闭，可能会导致未知bug</li>
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