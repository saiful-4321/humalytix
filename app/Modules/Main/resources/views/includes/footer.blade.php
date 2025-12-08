<footer class="footer" style="height:90px">
  <div class="container-fluid">
      <div class="row">
          <div class="col-sm-8 col-lg-3 py-0 d-none d-sm-block">
            <h6 class="d-block mt-1 text-info">{{ config('common.cms.title') }}</h6>
            <p class="d-block mt-0 text-muted">{{ config('common.cms.slogan') }}</p>
          </div>
          <div class="col-sm-6 py-0 d-none d-lg-block">
          </div>
          <div class="col-sm-4 col-lg-3 py-0">
              <div class="text-sm-end text-primary">
                {{ date("Y") }}&copy;{{ config('common.cms.short_title') }}
              </div>
              <div class="text-sm-end text-success">
                  Design & Develop by <a href="https://quantfintech.ai" class="text-decoration-underline">QFL</a>
              </div>
          </div>
      </div>
  </div>
</footer>