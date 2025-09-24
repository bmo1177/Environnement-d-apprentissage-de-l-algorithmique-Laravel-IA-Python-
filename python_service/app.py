#!/usr/bin/env python3
"""
AI Microservices for Learner Environment
=========================================
FastAPI application providing code evaluation, profiling, clustering, and recommendations.

Author: Learner Environment Research
"""

from fastapi import FastAPI, HTTPException, BackgroundTasks
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, Field
from typing import List, Dict, Any, Optional
import logging
import json
import numpy as np
from datetime import datetime

# Import service modules
from evaluator import CodeEvaluator
from profile import ProfileManager
from cluster import ClusteringService
from expert_rules import ExpertRulesEngine


# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)

# Initialize FastAPI app
app = FastAPI(
    title="Learner Environment AI Services",
    description="Microservices for code evaluation, learner profiling, and adaptive feedback",
    version="1.0.0"
)

# Configure CORS for Laravel integration
app.add_middleware(
    CORSMiddleware,
    allow_origins=["http://localhost:8000", "http://localhost", "*"],  # Adjust for production
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Initialize service instances with error handling
try:
    code_evaluator = CodeEvaluator()
    profile_manager = ProfileManager()
    clustering_service = ClusteringService()
    expert_rules = ExpertRulesEngine()
    logger.info("All services initialized successfully")
except Exception as e:
    logger.error(f"Service initialization failed: {str(e)}")
    # Initialize with safe defaults
    code_evaluator = None
    profile_manager = None
    clustering_service = None
    expert_rules = None


# Pydantic models for request/response validation
class EvaluationRequest(BaseModel):
    code: str = Field(..., description="Student's submitted code")
    test_cases: List[Dict[str, Any]] = Field(..., description="Test cases to run")
    language: str = Field(default="python", description="Programming language")
    timeout: int = Field(default=5, description="Execution timeout in seconds")
    
    class Config:
        json_schema_extra = {
            "example": {
                "code": "def two_sum(nums, target):\n    return [0, 1]",
                "test_cases": [
                    {"input": {"nums": [2, 7, 11, 15], "target": 9}, "output": [0, 1]},
                    {"input": {"nums": [3, 3], "target": 6}, "output": [0, 1]}
                ],
                "language": "python",
                "timeout": 5
            }
        }


class EvaluationResponse(BaseModel):
    success: bool
    test_results: List[Dict[str, Any]]
    score: int
    execution_time: Optional[float] = None
    memory_used: Optional[int] = None
    error: Optional[str] = None
    code_quality: Optional[Dict[str, Any]] = None


class ProfileUpdateRequest(BaseModel):
    user_id: int
    attempt_data: Dict[str, Any]
    challenge_data: Dict[str, Any]
    
    class Config:
        json_schema_extra = {
            "example": {
                "user_id": 1,
                "attempt_data": {
                    "is_successful": True,
                    "score": 80,
                    "time_spent": 300,
                    "hints_used": 2
                },
                "challenge_data": {
                    "competency_id": 1,
                    "difficulty": "medium",
                    "points": 100
                }
            }
        }


class ProfileUpdateResponse(BaseModel):
    success: bool
    profile: Dict[str, Any]
    updates: Dict[str, Any]
    message: str


class ClusterRequest(BaseModel):
    min_clusters: int = Field(default=3, ge=2, le=10)
    max_clusters: int = Field(default=6, ge=2, le=10)
    feature_data: Optional[List[Dict[str, float]]] = None
    force_regenerate: bool = Field(default=False, description="Force regeneration of sample data")
    
    class Config:
        json_schema_extra = {
            "example": {
                "min_clusters": 3,
                "max_clusters": 6,
                "feature_data": [
                    {"accuracy": 0.8, "speed": 0.6, "complexity": 0.4},
                    {"accuracy": 0.9, "speed": 0.7, "complexity": 0.5}
                ],
                "force_regenerate": False
            }
        }


class ClusterResponse(BaseModel):
    success: bool
    clusters: List[Dict[str, Any]]
    optimal_k: int
    silhouette_score: float
    message: str


class RecommendationRequest(BaseModel):
    attempt_id: int
    code: str
    test_results: List[Dict[str, Any]]
    error_message: Optional[str] = None
    user_profile: Optional[Dict[str, Any]] = None
    
    class Config:
        json_schema_extra = {
            "example": {
                "attempt_id": 123,
                "code": "def factorial(n):\n    return n * factorial(n-1)",
                "test_results": [
                    {"test_id": 1, "passed": False, "error": "RecursionError"}
                ],
                "error_message": "maximum recursion depth exceeded"
            }
        }


class RecommendationResponse(BaseModel):
    success: bool
    feedback: Dict[str, Any]
    hints: List[str]
    resources: List[Dict[str, str]]
    next_steps: List[str]
    confidence: float


# API Endpoints

@app.get("/")
async def root():
    """Health check and service information."""
    return {
        "service": "Learner Environment AI Services",
        "status": "operational",
        "version": "1.0.0",
        "endpoints": [
            "/evaluate",
            "/update_profile", 
            "/cluster",
            "/recommend"
        ],
        "timestamp": datetime.now().isoformat()
    }


@app.post("/evaluate", response_model=EvaluationResponse)
async def evaluate_code(request: EvaluationRequest):
    """
    Evaluate student code against test cases.
    
    This endpoint securely executes student code and returns test results,
    performance metrics, and code quality indicators.
    """
    if code_evaluator is None:
        raise HTTPException(status_code=503, detail="Code evaluator service not available")
    
    try:
        logger.info(f"Evaluating code: {len(request.code)} chars, {len(request.test_cases)} tests")
        
        # Perform evaluation
        result = code_evaluator.evaluate(
            code=request.code,
            test_cases=request.test_cases,
            language=request.language,
            timeout=request.timeout
        )
        
        # Calculate score
        passed_tests = sum(1 for tr in result['test_results'] if tr.get('passed', False))
        total_tests = len(request.test_cases)
        score = int((passed_tests / total_tests) * 100) if total_tests > 0 else 0
        
        return EvaluationResponse(
            success=result['success'],
            test_results=result['test_results'],
            score=score,
            execution_time=result.get('execution_time'),
            memory_used=result.get('memory_used'),
            error=result.get('error'),
            code_quality=result.get('code_quality')
        )
        
    except Exception as e:
        logger.error(f"Evaluation failed: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))


@app.post("/update_profile", response_model=ProfileUpdateResponse)
async def update_learner_profile(request: ProfileUpdateRequest, background_tasks: BackgroundTasks):
    """
    Update learner profile based on attempt data.
    
    This endpoint updates cognitive, behavioral, and motivational profiles
    based on student performance and engagement patterns.
    """
    if profile_manager is None:
        raise HTTPException(status_code=503, detail="Profile manager service not available")
    
    try:
        logger.info(f"Updating profile for user {request.user_id}")
        
        # Update profile
        result = profile_manager.update_profile(
            user_id=request.user_id,
            attempt_data=request.attempt_data,
            challenge_data=request.challenge_data
        )
        
        # Schedule background analysis if needed
        if result.get('requires_clustering', False):
            background_tasks.add_task(
                trigger_clustering_update,
                user_id=request.user_id
            )
        
        return ProfileUpdateResponse(
            success=True,
            profile=result['profile'],
            updates=result['updates'],
            message=result['message']
        )
        
    except Exception as e:
        logger.error(f"Profile update failed: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))


def generate_sample_data(n_samples: int = 50, n_features: int = 6) -> np.ndarray:
    """
    Generate synthetic student performance data for clustering analysis.
    
    Args:
        n_samples: Number of student samples to generate
        n_features: Number of performance features per student
    
    Returns:
        numpy.ndarray: Matrix of shape (n_samples, n_features) containing synthetic data
    """
    np.random.seed(42)  # For reproducible results
    
    # Generate three clusters with distinct characteristics
    cluster_centers = [
        [0.3, 0.2, 0.4, 0.3, 0.2, 0.1],  # Struggling students
        [0.7, 0.8, 0.6, 0.7, 0.5, 0.4],  # Average students  
        [0.9, 0.95, 0.9, 0.85, 0.8, 0.7] # Advanced students
    ]
    
    samples_per_cluster = n_samples // 3
    data = []
    
    for center in cluster_centers:
        # Generate samples around each center with some noise
        cluster_data = np.random.multivariate_normal(
            mean=center,
            cov=np.eye(n_features) * 0.01,  # Small covariance for tight clusters
            size=samples_per_cluster
        )
        # Clip values to [0, 1] range
        cluster_data = np.clip(cluster_data, 0, 1)
        data.append(cluster_data)
    
    # Handle remaining samples for the last cluster
    remaining = n_samples - (samples_per_cluster * 3)
    if remaining > 0:
        extra_data = np.random.multivariate_normal(
            mean=cluster_centers[-1],
            cov=np.eye(n_features) * 0.01,
            size=remaining
        )
        extra_data = np.clip(extra_data, 0, 1)
        data.append(extra_data)
    
    return np.vstack(data)


@app.post("/cluster", response_model=ClusterResponse)
async def perform_clustering(request: ClusterRequest):
    """
    Perform clustering analysis on student performance data.
    
    This endpoint identifies learning patterns and groups students
    with similar mistake patterns or learning behaviors.
    """
    if clustering_service is None:
        raise HTTPException(status_code=503, detail="Clustering service not available")
    
    try:
        logger.info(f"Performing clustering: k={request.min_clusters}-{request.max_clusters}")
        
        # Validate cluster range
        if request.min_clusters > request.max_clusters:
            raise HTTPException(
                status_code=400, 
                detail="min_clusters must be less than or equal to max_clusters"
            )
        
        # Use provided data or generate sample data
        if request.feature_data:
            # Convert list of dicts to numpy array
            try:
                feature_matrix = np.array([
                    list(sample.values()) for sample in request.feature_data
                ])
                logger.info(f"Using provided data: {feature_matrix.shape}")
            except Exception as e:
                logger.error(f"Failed to process feature_data: {str(e)}")
                raise HTTPException(
                    status_code=400,
                    detail=f"Invalid feature_data format: {str(e)}"
                )
        else:
            # Generate synthetic data
            n_samples = max(50, request.max_clusters * 10)  # Ensure sufficient samples
            feature_matrix = generate_sample_data(n_samples=n_samples, n_features=6)
            logger.info(f"Generated sample data: {feature_matrix.shape}")
        
        # Validate data shape
        if feature_matrix.shape[0] < request.max_clusters:
            raise HTTPException(
                status_code=400,
                detail=f"Not enough samples ({feature_matrix.shape[0]}) for {request.max_clusters} clusters"
            )
        
        # Perform clustering
        result = clustering_service.cluster_students(
            min_k=request.min_clusters,
            max_k=request.max_clusters,
            feature_data=feature_matrix
        )
        
        return ClusterResponse(
            success=True,
            clusters=result['clusters'],
            optimal_k=result['optimal_k'],
            silhouette_score=result['silhouette_score'],
            message=result['message']
        )
        
    except HTTPException:
        raise
    except Exception as e:
        logger.error(f"Clustering failed: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Clustering analysis failed: {str(e)}")


@app.post("/recommend", response_model=RecommendationResponse)
async def generate_recommendations(request: RecommendationRequest):
    """
    Generate personalized recommendations based on code analysis.
    
    This endpoint analyzes student code, identifies mistake patterns,
    and generates targeted feedback using expert rules and ML models.
    """
    if expert_rules is None:
        raise HTTPException(status_code=503, detail="Expert rules service not available")
    
    try:
        logger.info(f"Generating recommendations for attempt {request.attempt_id}")
        
        # Extract features from code
        code_features = expert_rules.extract_code_features(
            code=request.code,
            test_results=request.test_results,
            error_message=request.error_message
        )
        
        # Apply expert rules
        feedback = expert_rules.generate_feedback(
            features=code_features,
            user_profile=request.user_profile
        )
        
        # Get cluster-based recommendations if available
        if clustering_service and clustering_service.model_loaded:
            cluster_feedback = clustering_service.get_cluster_recommendation(code_features)
            feedback['cluster_insight'] = cluster_feedback
        
        return RecommendationResponse(
            success=True,
            feedback=feedback['primary_feedback'],
            hints=feedback['hints'],
            resources=feedback['resources'],
            next_steps=feedback['next_steps'],
            confidence=feedback['confidence']
        )
        
    except Exception as e:
        logger.error(f"Recommendation generation failed: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))


# Utility endpoints for debugging and monitoring

@app.get("/stats")
async def get_statistics():
    """Get service statistics and usage metrics."""
    stats = {
        "timestamp": datetime.now().isoformat(),
        "service_status": {
            "evaluator": code_evaluator is not None,
            "profile_manager": profile_manager is not None,
            "clustering": clustering_service is not None,
            "expert_rules": expert_rules is not None
        }
    }
    
    try:
        if code_evaluator:
            stats["evaluations_processed"] = code_evaluator.get_stats()
    except Exception as e:
        logger.warning(f"Could not get evaluator stats: {e}")
        
    try:
        if profile_manager:
            stats["profiles_updated"] = profile_manager.get_stats()
    except Exception as e:
        logger.warning(f"Could not get profile manager stats: {e}")
        
    try:
        if clustering_service:
            stats["clusters_analyzed"] = clustering_service.get_stats()
    except Exception as e:
        logger.warning(f"Could not get clustering stats: {e}")
        
    try:
        if expert_rules:
            stats["recommendations_generated"] = expert_rules.get_stats()
    except Exception as e:
        logger.warning(f"Could not get expert rules stats: {e}")
    
    return stats


@app.get("/health")
async def health_check():
    """Detailed health check for all services."""
    health_status = {
        "overall": "healthy",
        "services": {},
        "timestamp": datetime.now().isoformat()
    }
    
    # Check each service individually
    services = [
        ("evaluator", code_evaluator),
        ("profile_manager", profile_manager),
        ("clustering", clustering_service),
        ("expert_rules", expert_rules)
    ]
    
    for service_name, service in services:
        try:
            if service is not None:
                health_status["services"][service_name] = service.health_check()
            else:
                health_status["services"][service_name] = False
        except Exception as e:
            logger.error(f"Health check failed for {service_name}: {e}")
            health_status["services"][service_name] = False
    
    # Check if any service is unhealthy
    if any(not status for status in health_status["services"].values()):
        health_status["overall"] = "degraded"
    
    return health_status


# Background tasks

async def trigger_clustering_update(user_id: int):
    """Background task to update clustering after significant profile changes."""
    try:
        logger.info(f"Background clustering update for user {user_id}")
        if clustering_service:
            # Generate fresh data for re-clustering
            feature_data = generate_sample_data(n_samples=100, n_features=6)
            result = clustering_service.cluster_students(
                min_k=3,
                max_k=6,
                feature_data=feature_data
            )
            logger.info(f"Background clustering completed: {result['optimal_k']} clusters")
    except Exception as e:
        logger.error(f"Background clustering failed: {str(e)}")


if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)