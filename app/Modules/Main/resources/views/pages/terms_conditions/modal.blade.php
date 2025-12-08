<a href="javascript:void(0);" class="text-info" data-bs-toggle="modal" data-bs-target=".agreeModal">Terms & Conditions, Privacy Policy, and Return Refund Policy</a>

@push("styles")
<style> 
    .agreeModal h1 {
        color: black;
        font-family: "Arial Narrow", sans-serif;
        font-style: normal;
        font-weight: bold;
        text-decoration: none;
        font-size: 11pt;
    }
    .agreeModal p {
        color: black;
        font-family: "Arial Narrow", sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 11pt;
        margin: 0pt;
    }
    .agreeModal .s1 {
        color: black;
        font-family: "Arial Narrow", sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 6pt;
    }
    .agreeModal .s2 {
        color: black;
        font-family: "Arial Narrow", sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 8pt;
    }
    .agreeModal .s3 {
        color: black;
        font-family: "Arial Narrow", sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 5pt;
        vertical-align: 2pt;
    }
    .agreeModal .s4 {
        color: #9A9A9A;
        font-family: "Arial Narrow", sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 8pt;
    }
    .agreeModal li {
        display: block;
    }

    .agreeModal #l1 {
        padding-left: 0pt;
        counter-reset: c1 1;
    }
    .agreeModal #l1>li>*:first-child:before {
        counter-increment: c1;
        content: counter(c1, decimal)". ";
        color: black;
        font-family: "Arial Narrow", sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 11pt;
    }
    .agreeModal #l1>li:first-child>*:first-child:before {
        counter-increment: c1 0;
    }
    .agreeModal #l2 {
        padding-left: 0pt;
        counter-reset: c2 1;
    }
    .agreeModal #l2>li>*:first-child:before {
        counter-increment: c2;
        content: "(" counter(c2, lower-latin)") ";
        color: black;
        font-family: "Arial Narrow", sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 11pt;
    }
    .agreeModal #l2>li:first-child>*:first-child:before {
        counter-increment: c2 0;
    }

    .agreeModal #l3 {
        padding-left: 0pt;
        counter-reset: d1 1;
    }
    .agreeModal #l3>li>*:first-child:before {
        counter-increment: d1;
        content: counter(d1, lower-latin)") ";
        color: black;
        font-family: "Arial Narrow", sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 11pt;
    }
    .agreeModal #l3>li:first-child>*:first-child:before {
        counter-increment: d1 0;
    }
    .agreeModal #l4 {
        padding-left: 0pt;
        counter-reset: c2 1;
    }
    .agreeModal #l4>li>*:first-child:before {
        counter-increment: c2;
        content: "(" counter(c2, lower-latin)") ";
        color: black;
        font-family: "Arial Narrow", sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 11pt;
    }
    .agreeModal #l4>li:first-child>*:first-child:before {
        counter-increment: c2 0;
    }
    .agreeModal #l5 {
        padding-left: 0pt;
        counter-reset: e1 1;
    }
    .agreeModal #l5>li>*:first-child:before {
        counter-increment: e1;
        content: counter(e1, lower-latin)") ";
        color: black;
        font-family: "Arial Narrow", sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 11pt;
    }
    .agreeModal #l5>li:first-child>*:first-child:before {
        counter-increment: e1 0;
    }
    .agreeModal #l6 {
        padding-left: 0pt;
        counter-reset: e2 1;
    }
    .agreeModal #l6>li>*:first-child:before {
        counter-increment: e2;
        content: "(" counter(e2, lower-roman)") ";
        color: black;
        font-family: "Arial Narrow", sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 11pt;
    }
    .agreeModal #l6>li:first-child>*:first-child:before {
        counter-increment: e2 0;
    }
    .agreeModal #l7 {
        padding-left: 0pt;
        counter-reset: c2 1;
    }
    .agreeModal #l7>li>*:first-child:before {
        counter-increment: c2;
        content: "(" counter(c2, lower-latin)") ";
        color: black;
        font-family: "Arial Narrow", sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 11pt;
    }
    .agreeModal #l7>li:first-child>*:first-child:before {
        counter-increment: c2 0;
    }
</style>
@endpush

<div class="modal fade agreeModal" tabindex="-1" aria-labelledby="agreeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="agreeModalLabel">Terms & Conditions, Privacy Policy, and Return Refund Policy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body agreeModalBody" style="max-height:50vh; overflow-y: auto;">
                <div class="container">
                    @include("BoAccount::pages.terms_conditions.text")
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary agreeButton" style="display: none;">Agree</button>
            </div>
        </div>
    </div>
</div>

@pushOnce('scripts')
@if (!empty($checkbox_name))
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const agreeModal  = document.querySelector('.agreeModal');
        const modalBody   = document.querySelector('.agreeModalBody');
        const agreeButton = document.querySelector('.agreeButton');

        modalBody.addEventListener('scroll', () => {
            if (modalBody.scrollTop + modalBody.clientHeight >= modalBody.scrollHeight) {
                agreeButton.style.display = 'block';
            } else {
                agreeButton.style.display = 'none';
            }
        });

        agreeButton.addEventListener('click', function() {
            var checkbox = document.querySelector('input[name="{{ $checkbox_name ?? "" }}"]');
            if (checkbox) {
                checkbox.checked = true;
                $(agreeModal).modal('hide');
            }
        });
    });
</script>
@endif
@endPushOnce
