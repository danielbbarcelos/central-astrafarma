@extends('layouts.template')

@section('page-title', 'Novo pedido de venda')

@section('page-css')

    <link rel="stylesheet" href="https://unpkg.com/materialize-stepper@3.0.1/dist/css/mstepper.min.css">
    <style>
        .btn,
        .btn-large,
        .btn-small,
        .btn-flat {
            border-radius: 4px;
            font-weight: 500;
        }

        .card:hover {
            box-shadow: 0px 10px 35px 0px rgba(0, 0, 0, 0.18);
        }

        .card {
            border-radius: 15px;
            box-shadow: 0px 5px 25px 0px rgba(0, 0, 0, 0.15);
        }
    </style>
@endsection

@section('page-breadcrumbs')

    <div class="breadcrumbs">
        <ul class="breadcrumbs-itens breadcrumbs_chevron">
            <li class="breadcrumbs__item"><a href="{{url('/pedidos-vendas')}}" class="breadcrumbs__element">Lista de pedidos</a></li>
            <li class="breadcrumbs__item breadcrumbs__item_active"><span class="breadcrumbs__element">Novo pedido de venda</span></li>
        </ul>
    </div>

@endsection

@section('page-content')

    <!--
Materializecss Stepper v3.0.0-beta.1 - Igor Marcossi
https://github.com/Kinark/Materialize-stepper
-->
    <div class="section grey lighten-5">
        <div class="container">
            <div class="row">
                <div class="col xl4 l6 m10 s12 offset-xl4 offset-l3 offset-m1">
                    <h3 class="light center-align blue-text">Sign up form</h3>
                    <div class="card">
                        <div class="card-content">

                            <ul data-method="GET" class="stepper non-linear">
                                <li class="step active">
                                    <div class="step-title waves-effect waves-dark">E-mail</div>
                                    <div class="step-content">
                                        <div class="row">
                                            <div class="input-field col s12">
                                                <input id="email" name="email" type="email" class="validate" required>
                                                <label for="email">Your e-mail</label>
                                            </div>
                                        </div>
                                        <div class="step-actions">
                                            <button class="waves-effect waves-dark btn blue next-step" data-feedback="anyThing">Continue</button>
                                        </div>
                                    </div>
                                </li>
                                <li class="step">
                                    <div class="step-title waves-effect waves-dark">Step 2</div>
                                    <div class="step-content">
                                        <div class="row">
                                            <div class="input-field col s12">
                                                <input id="password" name="password" type="password" class="validate" required>
                                                <label for="password">Your password</label>
                                            </div>
                                        </div>
                                        <div class="step-actions">
                                            <button class="waves-effect waves-dark btn blue next-step">CONTINUE</button>
                                            <button class="waves-effect waves-dark btn-flat previous-step">BACK</button>
                                        </div>
                                    </div>
                                </li>
                                <li class="step">
                                    <div class="step-title waves-effect waves-dark">Callback</div>
                                    <div class="step-content">
                                        End!!!!!
                                        <div class="step-actions">
                                            <button class="waves-effect waves-dark btn blue next-step" data-feedback="noThing">ENDLESS CALLBACK!</button>
                                        </div>
                                    </div>
                                </li>
                            </ul>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('pages.pedidos-vendas.modal-produto')

@endsection

@section('page-scripts')

    <script src="https://unpkg.com/materialize-stepper@3.0.1/dist/js/mstepper.min.js"></script>
    <script>

        var stepper = document.querySelector('stepper');
        var stepperInstace = new MStepper(stepper, {
            // Default active step.
            firstActive: 0,
            // Allow navigation by clicking on the next and previous steps on linear steppers.
            linearStepsNavigation: true,
            // Auto focus on first input of each step.
            autoFocusInput: false,
            // Set if a loading screen will appear while feedbacks functions are running.
            showFeedbackPreloader: true,
            // Auto generation of a form around the stepper.
            autoFormCreation: true,
            // Function to be called everytime a nextstep occurs. It receives 2 arguments, in this sequece: stepperForm, activeStepContent.
            //validationFunction: defaultValidationFunction, // more about this default functions below
            // Enable or disable navigation by clicking on step-titles
            stepTitleNavigation: true,
            // Preloader used when step is waiting for feedback function. If not defined, Materializecss spinner-blue-only will be used.
            feedbackPreloader: '<div class="spinner-layer spinner-blue-only">...</div>'
        })
    </script>


@endsection

