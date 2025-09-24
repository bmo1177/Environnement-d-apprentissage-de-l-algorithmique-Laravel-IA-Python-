{{-- resources/views/layouts/partials/footer.blade.php --}}
<footer class="bg-light mt-5 py-4 border-top">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h6 class="fw-bold">{{ config('app.name', 'Laravel Learner') }}</h6>
                <p class="text-muted small mb-0">
                    Adaptive algorithmic learning environment powered by AI
                </p>
            </div>
            
            <div class="col-md-3">
                <h6 class="fw-bold">Resources</h6>
                <ul class="list-unstyled small">
                    <li><a href="#" class="text-decoration-none">Help Center</a></li>
                    <li><a href="#" class="text-decoration-none">Documentation</a></li>
                    <li><a href="#" class="text-decoration-none">API Reference</a></li>
                </ul>
            </div>
            
            <div class="col-md-3">
                <h6 class="fw-bold">System Status</h6>
                <div class="small">
                    <div class="d-flex align-items-center mb-1">
                        <div class="bg-success rounded-circle me-2" style="width: 8px; height: 8px;"></div>
                        <span class="text-muted">Laravel Backend</span>
                    </div>
                    <div class="d-flex align-items-center mb-1">
                        <div class="bg-success rounded-circle me-2" style="width: 8px; height: 8px;"></div>
                        <span class="text-muted">Python AI Services</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-success rounded-circle me-2" style="width: 8px; height: 8px;"></div>
                        <span class="text-muted">Database</span>
                    </div>
                </div>
            </div>
        </div>
        
        <hr class="my-3">
        
        <div class="row align-items-center">
            <div class="col-md-8">
                <p class="text-muted small mb-0">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. Built for algorithmic learning research.
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <small class="text-muted">
                    Version 1.0.0 | 
                    <span class="text-success">
                        <i class="bi bi-circle-fill" style="font-size: 6px;"></i>
                        All systems operational
                    </span>
                </small>
            </div>
        </div>
    </div>
</footer>