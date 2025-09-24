#!/usr/bin/env python3
"""
Clustering Service Module
=========================
Provides student clustering based on performance and behavioral patterns.

Uses K-means clustering with automatic parameter selection based on
silhouette scores. Can integrate with existing dataset patterns.
"""

import numpy as np
import json
import logging
from typing import List, Dict, Any, Optional, Tuple
from pathlib import Path
from sklearn.cluster import KMeans
from sklearn.preprocessing import StandardScaler
from sklearn.metrics import silhouette_score
import pickle

logger = logging.getLogger(__name__)


class ClusteringService:
    """
    Clustering service for grouping students by learning patterns.
    
    Supports both real-time clustering and pre-trained model loading.
    """
    
    def __init__(self, model_path: str = "./models"):
        self.model_path = Path(model_path)
        self.model_path.mkdir(exist_ok=True)
        
        self.kmeans_model = None
        self.scaler = StandardScaler()
        self.cluster_centers = None
        self.cluster_metadata = {}
        self.model_loaded = False
        self.clustering_count = 0
        self.n_features = 6  # Fixed feature dimensionality
        
        # Try to load existing model
        self._load_model()
    
    def cluster_students(self,
                        min_k: int = 3,
                        max_k: int = 6,
                        feature_data: Optional[np.ndarray] = None) -> Dict[str, Any]:
        """
        Perform clustering on student data.
        
        Args:
            min_k: Minimum number of clusters to test
            max_k: Maximum number of clusters to test
            feature_data: Optional numpy array of shape (n_samples, n_features)
            
        Returns:
            Clustering results including labels and metrics
        """
        self.clustering_count += 1
        
        # Validate input parameters
        if min_k >= max_k:
            return {
                'success': False,
                'message': 'min_clusters must be less than max_clusters',
                'clusters': [],
                'optimal_k': min_k,
                'silhouette_score': 0.0
            }
        
        # Prepare feature matrix
        if feature_data is not None:
            X = self._validate_and_prepare_features(feature_data)
        else:
            # Generate synthetic data for testing
            X = self._generate_synthetic_features()
        
        if X is None or X.shape[0] < min_k:
            return {
                'success': False,
                'message': f'Insufficient data for clustering. Need at least {min_k} samples, got {X.shape[0] if X is not None else 0}',
                'clusters': [],
                'optimal_k': min_k,
                'silhouette_score': 0.0
            }
        
        # Validate cluster count against sample size
        max_feasible_k = min(max_k, X.shape[0] - 1)
        if min_k > max_feasible_k:
            return {
                'success': False,
                'message': f'Cannot create {min_k} clusters with only {X.shape[0]} samples',
                'clusters': [],
                'optimal_k': min_k,
                'silhouette_score': 0.0
            }
        
        try:
            # Find optimal k using silhouette score
            optimal_k, best_score = self._find_optimal_k(X, min_k, max_feasible_k)
            
            # Perform final clustering with optimal k
            X_scaled = self.scaler.fit_transform(X)
            self.kmeans_model = KMeans(n_clusters=optimal_k, random_state=42, n_init=10)
            labels = self.kmeans_model.fit_predict(X_scaled)
            
            # Validate clustering results
            unique_labels = np.unique(labels)
            if len(unique_labels) != optimal_k:
                logger.warning(f"Expected {optimal_k} clusters, got {len(unique_labels)}")
            
            # Save model
            self._save_model()
            self.model_loaded = True
            
            # Generate cluster analysis
            clusters = self._analyze_clusters(X, labels, X_scaled)
            
            return {
                'success': True,
                'clusters': clusters,
                'optimal_k': optimal_k,
                'silhouette_score': best_score,
                'message': f'Successfully clustered {X.shape[0]} samples into {optimal_k} groups'
            }
            
        except Exception as e:
            logger.error(f"Clustering failed: {str(e)}")
            return {
                'success': False,
                'message': f'Clustering failed: {str(e)}',
                'clusters': [],
                'optimal_k': min_k,
                'silhouette_score': 0.0
            }
    
    def _validate_and_prepare_features(self, feature_data: np.ndarray) -> Optional[np.ndarray]:
        """
        Validate and prepare feature data ensuring correct dimensionality.
        
        Args:
            feature_data: Input feature matrix
            
        Returns:
            Validated and potentially reshaped feature matrix
        """
        if not isinstance(feature_data, np.ndarray):
            logger.error(f"Feature data must be numpy array, got {type(feature_data)}")
            return None
        
        # Ensure 2D array
        if feature_data.ndim == 1:
            feature_data = feature_data.reshape(1, -1)
        elif feature_data.ndim != 2:
            logger.error(f"Feature data must be 1D or 2D, got {feature_data.ndim}D")
            return None
        
        n_samples, n_features = feature_data.shape
        
        # Validate sample count
        if n_samples < 2:
            logger.error(f"Need at least 2 samples for clustering, got {n_samples}")
            return None
        
        # Handle feature dimension mismatch
        if n_features != self.n_features:
            logger.warning(f"Expected {self.n_features} features, got {n_features}")
            
            if n_features > self.n_features:
                # Truncate excess features
                feature_data = feature_data[:, :self.n_features]
                logger.info(f"Truncated to {self.n_features} features")
            else:
                # Pad with zeros for missing features
                padding = np.zeros((n_samples, self.n_features - n_features))
                feature_data = np.hstack([feature_data, padding])
                logger.info(f"Padded to {self.n_features} features with zeros")
        
        # Validate for NaN or infinite values
        if not np.isfinite(feature_data).all():
            logger.warning("Feature data contains NaN or infinite values, replacing with zeros")
            feature_data = np.nan_to_num(feature_data, nan=0.0, posinf=0.0, neginf=0.0)
        
        return feature_data
    
    def _generate_synthetic_features(self, n_samples: int = 50) -> np.ndarray:
        """
        Generate synthetic student performance data with proper dimensionality.
        
        Args:
            n_samples: Number of synthetic samples to generate
            
        Returns:
            Synthetic feature matrix of shape (n_samples, self.n_features)
        """
        np.random.seed(42)  # Ensure reproducibility
        
        # Define cluster prototypes with correct dimensionality
        cluster_prototypes = [
            [0.3, 0.2, 0.4, 0.3, 0.2, 0.1],  # Struggling students
            [0.7, 0.8, 0.6, 0.7, 0.5, 0.4],  # Average students  
            [0.9, 0.95, 0.9, 0.85, 0.8, 0.7] # Advanced students
        ]
        
        samples_per_cluster = n_samples // len(cluster_prototypes)
        features = []
        
        for prototype in cluster_prototypes:
            # Ensure prototype has correct dimensionality
            if len(prototype) != self.n_features:
                prototype = prototype[:self.n_features] + [0.5] * max(0, self.n_features - len(prototype))
            
            # Generate samples around prototype
            cluster_samples = np.random.multivariate_normal(
                mean=prototype,
                cov=np.eye(self.n_features) * 0.01,  # Small covariance
                size=samples_per_cluster
            )
            
            # Clip to valid range [0, 1]
            cluster_samples = np.clip(cluster_samples, 0, 1)
            features.append(cluster_samples)
        
        # Handle remaining samples
        remaining = n_samples - (samples_per_cluster * len(cluster_prototypes))
        if remaining > 0:
            extra_samples = np.random.multivariate_normal(
                mean=cluster_prototypes[-1],
                cov=np.eye(self.n_features) * 0.01,
                size=remaining
            )
            extra_samples = np.clip(extra_samples, 0, 1)
            features.append(extra_samples)
        
        result = np.vstack(features)
        logger.info(f"Generated synthetic data: {result.shape}")
        return result
    
    def _find_optimal_k(self, X: np.ndarray, min_k: int, max_k: int) -> Tuple[int, float]:
        """
        Find optimal number of clusters using silhouette analysis.
        
        Args:
            X: Feature matrix of shape (n_samples, n_features)
            min_k: Minimum number of clusters to test
            max_k: Maximum number of clusters to test
            
        Returns:
            Tuple of (optimal_k, best_silhouette_score)
        """
        # Ensure we have enough samples
        max_feasible_k = min(max_k, X.shape[0] - 1)
        if min_k > max_feasible_k:
            logger.warning(f"Adjusted max_k from {max_k} to {max_feasible_k}")
            max_k = max_feasible_k
        
        # Scale features for clustering
        X_scaled = self.scaler.fit_transform(X)
        
        best_k = min_k
        best_score = -1
        scores = []
        
        for k in range(min_k, max_k + 1):
            try:
                kmeans = KMeans(n_clusters=k, random_state=42, n_init=10)
                labels = kmeans.fit_predict(X_scaled)
                
                # Ensure we have the expected number of clusters
                unique_labels = np.unique(labels)
                if len(unique_labels) < 2:
                    logger.warning(f"Only {len(unique_labels)} unique clusters found for k={k}")
                    continue
                
                # Calculate silhouette score
                score = silhouette_score(X_scaled, labels)
                scores.append((k, score))
                
                if score > best_score:
                    best_score = score
                    best_k = k
                
                logger.debug(f"k={k}, silhouette_score={score:.3f}")
                
            except Exception as e:
                logger.warning(f"Failed to evaluate k={k}: {str(e)}")
                continue
        
        if not scores:
            logger.error("No valid clustering solutions found")
            return min_k, -1
        
        logger.info(f"Optimal k={best_k} with silhouette score={best_score:.3f}")
        return best_k, best_score
    
    def _analyze_clusters(self, X: np.ndarray, labels: np.ndarray, X_scaled: np.ndarray) -> List[Dict[str, Any]]:
        """
        Analyze cluster characteristics and generate interpretations.
        
        Args:
            X: Original feature matrix
            labels: Cluster labels for each sample
            X_scaled: Scaled feature matrix used for clustering
            
        Returns:
            List of cluster analysis dictionaries
        """
        clusters = []
        unique_labels = np.unique(labels)
        
        # Ensure we have cluster centers
        if self.kmeans_model is None or not hasattr(self.kmeans_model, 'cluster_centers_'):
            logger.error("No cluster centers available")
            return clusters
        
        centers = self.kmeans_model.cluster_centers_
        
        for label in unique_labels:
            cluster_mask = labels == label
            cluster_data = X[cluster_mask]
            
            if len(cluster_data) == 0:
                continue
            
            # Ensure label is within valid range
            if label >= len(centers):
                logger.warning(f"Invalid cluster label {label}, max available: {len(centers)-1}")
                continue
            
            # Calculate cluster statistics
            cluster_info = {
                'cluster_id': int(label),
                'size': int(np.sum(cluster_mask)),
                'centroid': centers[label].tolist(),
                'characteristics': self._interpret_cluster(centers[label])
            }
            
            # Add performance metrics
            if cluster_data.shape[1] >= 3:
                cluster_info['avg_cognitive'] = float(np.mean(cluster_data[:, 0]))
                cluster_info['avg_behavioral'] = float(np.mean(cluster_data[:, 1]))
                cluster_info['avg_motivational'] = float(np.mean(cluster_data[:, 2]))
            
            # Add statistical measures
            cluster_info['std_deviation'] = float(np.mean(np.std(cluster_data, axis=0)))
            cluster_info['inertia'] = float(np.sum((X_scaled[cluster_mask] - centers[label]) ** 2))
            
            clusters.append(cluster_info)
        
        # Sort clusters by size (descending)
        clusters.sort(key=lambda x: x['size'], reverse=True)
        
        # Store metadata for later use
        self.cluster_metadata = {
            'clusters': clusters,
            'timestamp': str(np.datetime64('now')),
            'n_samples': len(X),
            'n_features': X.shape[1]
        }
        
        return clusters
    
    def _interpret_cluster(self, centroid: np.ndarray) -> Dict[str, str]:
        """
        Generate human-readable interpretation of cluster characteristics.
        
        Args:
            centroid: Cluster centroid in scaled space
            
        Returns:
            Dictionary of interpretations for different aspects
        """
        interpretation = {}
        
        if len(centroid) < 3:
            interpretation['note'] = 'Insufficient features for detailed interpretation'
            return interpretation
        
        # Transform centroid back to original scale for interpretation
        # Note: This assumes StandardScaler was used
        try:
            # Inverse transform if scaler is fitted
            if hasattr(self.scaler, 'mean_'):
                original_centroid = self.scaler.inverse_transform(centroid.reshape(1, -1))[0]
            else:
                original_centroid = centroid
        except Exception:
            original_centroid = centroid
        
        # Assuming first 3 dimensions are cognitive, behavioral, motivational
        cog, beh, mot = original_centroid[:3]
        
        # Cognitive interpretation (normalized to 0-1 scale)
        if cog > 0.7:
            interpretation['cognitive'] = 'High problem-solving ability'
        elif cog < 0.3:
            interpretation['cognitive'] = 'Needs foundational support'
        else:
            interpretation['cognitive'] = 'Average cognitive performance'
        
        # Behavioral interpretation
        if beh > 0.7:
            interpretation['behavioral'] = 'Consistent and engaged'
        elif beh < 0.3:
            interpretation['behavioral'] = 'Irregular participation'
        else:
            interpretation['behavioral'] = 'Moderate engagement'
        
        # Motivational interpretation
        if mot > 0.7:
            interpretation['motivational'] = 'Highly motivated'
        elif mot < 0.3:
            interpretation['motivational'] = 'Low motivation'
        else:
            interpretation['motivational'] = 'Average motivation'
        
        # Overall recommendation
        avg_score = (cog + beh + mot) / 3
        if avg_score > 0.7:
            interpretation['recommendation'] = 'Advanced challenges recommended'
        elif avg_score < 0.4:
            interpretation['recommendation'] = 'Provide additional support and encouragement'
        else:
            interpretation['recommendation'] = 'Continue with current pace'
        
        # Performance category
        if avg_score > 0.8:
            interpretation['category'] = 'Advanced Learners'
        elif avg_score > 0.6:
            interpretation['category'] = 'Proficient Learners'
        elif avg_score > 0.4:
            interpretation['category'] = 'Developing Learners'
        else:
            interpretation['category'] = 'Beginning Learners'
        
        return interpretation
    
    def get_cluster_recommendation(self, student_features: Dict[str, float]) -> Dict[str, Any]:
        """
        Get cluster-based recommendations for a specific student.
        
        Args:
            student_features: Dictionary of student performance features
            
        Returns:
            Cluster recommendation results
        """
        if not self.model_loaded or self.kmeans_model is None:
            return {
                'success': False,
                'message': 'No clustering model available'
            }
        
        try:
            # Prepare features with proper dimensionality
            feature_vector = np.array([[
                student_features.get('cognitive_score', 50.0) / 100.0,  # Normalize to 0-1
                student_features.get('behavioral_score', 50.0) / 100.0,
                student_features.get('motivational_score', 50.0) / 100.0,
                student_features.get('success_rate', 0.5),
                student_features.get('avg_time', 300.0) / 600.0,  # Normalize assuming max 600 seconds
                student_features.get('attempts_count', 1.0) / 10.0  # Normalize assuming max 10 attempts
            ]])
            
            # Ensure correct dimensionality
            if feature_vector.shape[1] != self.n_features:
                feature_vector = self._validate_and_prepare_features(feature_vector)
                if feature_vector is None:
                    return {'success': False, 'message': 'Invalid feature dimensions'}
            
            # Scale and predict
            feature_scaled = self.scaler.transform(feature_vector)
            cluster_label = self.kmeans_model.predict(feature_scaled)[0]
            
            # Get cluster info
            cluster_info = None
            for cluster in self.cluster_metadata.get('clusters', []):
                if cluster['cluster_id'] == cluster_label:
                    cluster_info = cluster
                    break
            
            if cluster_info:
                return {
                    'success': True,
                    'cluster_id': int(cluster_label),
                    'cluster_size': cluster_info['size'],
                    'characteristics': cluster_info['characteristics'],
                    'peers_count': cluster_info['size'] - 1,
                    'category': cluster_info['characteristics'].get('category', 'Unknown')
                }
            
            return {
                'success': False,
                'message': 'Cluster information not found'
            }
            
        except Exception as e:
            logger.error(f"Cluster recommendation failed: {str(e)}")
            return {
                'success': False,
                'message': f'Recommendation failed: {str(e)}'
            }
    
    def _save_model(self) -> None:
        """Save clustering model and metadata."""
        if self.kmeans_model is None:
            return
        
        try:
            # Save KMeans model
            model_file = self.model_path / "kmeans_model.pkl"
            with open(model_file, 'wb') as f:
                pickle.dump(self.kmeans_model, f)
            
            # Save scaler
            scaler_file = self.model_path / "scaler.pkl"
            with open(scaler_file, 'wb') as f:
                pickle.dump(self.scaler, f)
            
            # Save metadata
            meta_file = self.model_path / "cluster_metadata.json"
            with open(meta_file, 'w') as f:
                json.dump(self.cluster_metadata, f, indent=2)
            
            # Save feature configuration
            config_file = self.model_path / "config.json"
            config = {
                'n_features': self.n_features,
                'model_version': '1.0',
                'last_updated': str(np.datetime64('now'))
            }
            with open(config_file, 'w') as f:
                json.dump(config, f, indent=2)
            
            logger.info(f"Model saved to {self.model_path}")
            
        except Exception as e:
            logger.error(f"Failed to save model: {str(e)}")
    
    def _load_model(self) -> bool:
        """Load existing clustering model if available."""
        model_file = self.model_path / "kmeans_model.pkl"
        scaler_file = self.model_path / "scaler.pkl"
        meta_file = self.model_path / "cluster_metadata.json"
        config_file = self.model_path / "config.json"
        
        if model_file.exists() and scaler_file.exists():
            try:
                # Load configuration first
                if config_file.exists():
                    with open(config_file, 'r') as f:
                        config = json.load(f)
                        self.n_features = config.get('n_features', 6)
                
                with open(model_file, 'rb') as f:
                    self.kmeans_model = pickle.load(f)
                
                with open(scaler_file, 'rb') as f:
                    self.scaler = pickle.load(f)
                
                if meta_file.exists():
                    with open(meta_file, 'r') as f:
                        self.cluster_metadata = json.load(f)
                
                self.model_loaded = True
                logger.info("Clustering model loaded successfully")
                return True
                
            except Exception as e:
                logger.error(f"Failed to load model: {str(e)}")
                self.model_loaded = False
        
        return False
    
    def get_stats(self) -> Dict[str, Any]:
        """Get clustering service statistics."""
        return {
            "clustering_performed": self.clustering_count,
            "model_loaded": self.model_loaded,
            "clusters_count": len(self.cluster_metadata.get('clusters', [])),
            "model_path": str(self.model_path),
            "n_features": self.n_features,
            "last_clustering": self.cluster_metadata.get('timestamp', 'Never')
        }
    
    def health_check(self) -> bool:
        """Check if clustering service is healthy."""
        try:
            # Test with properly dimensioned synthetic data
            test_data = self._generate_synthetic_features(n_samples=10)
            
            # Test clustering
            kmeans = KMeans(n_clusters=2, random_state=42, n_init=10)
            labels = kmeans.fit_predict(test_data)
            
            # Verify clustering worked
            unique_labels = np.unique(labels)
            return len(unique_labels) == 2 and test_data.shape[1] == self.n_features
            
        except Exception as e:
            logger.error(f"Health check failed: {str(e)}")
            return False


# Example integration with dataset patterns
def integrate_dataset_patterns(dataset_path: str = "dataset.json") -> Dict[str, Any]:
    """
    Integrate with actual dataset to extract mistake patterns for clustering.
    
    Args:
        dataset_path: Path to dataset JSON file
        
    Returns:
        Dictionary containing mistake patterns and statistics
    """
    dataset_file = Path(dataset_path)
    
    if not dataset_file.exists():
        logger.warning(f"Dataset not found at {dataset_path}")
        return {}
    
    try:
        with open(dataset_file, 'r') as f:
            dataset = json.load(f)
        
        # Extract patterns from variants
        patterns = {}
        mistake_categories = set()
        
        for problem in dataset:
            problem_id = problem.get('id', 'unknown')
            for variant in problem.get('variants', []):
                mistake_label = variant.get('mistake_label')
                if mistake_label:
                    patterns[mistake_label] = patterns.get(mistake_label, 0) + 1
                    mistake_categories.add(mistake_label)
        
        # Generate feature vectors based on mistake patterns
        feature_vectors = []
        for mistake_type in mistake_categories:
            # Create feature vector representing this mistake pattern
            # This is a simplified example - actual implementation would be more sophisticated
            frequency = patterns[mistake_type]
            feature_vector = [
                frequency / sum(patterns.values()),  # Relative frequency
                len(mistake_type) / 50.0,  # Complexity indicator
                hash(mistake_type) % 100 / 100.0,  # Pseudo-random feature
                0.5,  # Placeholder features
                0.5,
                0.5
            ]
            feature_vectors.append(feature_vector)
        
        return {
            'mistake_patterns': patterns,
            'total_variants': sum(patterns.values()),
            'unique_mistakes': len(mistake_categories),
            'feature_vectors': feature_vectors
        }
        
    except Exception as e:
        logger.error(f"Failed to load dataset: {str(e)}")
        return {}