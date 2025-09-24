#!/usr/bin/env python3
"""
Learner Profile Manager
=======================
Updates and maintains learner profiles based on performance data.

This module handles cognitive, behavioral, and motivational profile updates,
tracking student progress and adapting learning parameters.
"""

import json
import logging
from typing import Dict, Any, Optional, List
from datetime import datetime, timedelta
from pathlib import Path
import numpy as np

logger = logging.getLogger(__name__)


class ProfileManager:
    """
    Manages learner profiles with cognitive, behavioral, and motivational dimensions.
    
    In production, this would integrate with a database (PostgreSQL/MongoDB).
    Currently uses JSON files for demonstration.
    """
    
    def __init__(self, storage_path: str = "./profiles"):
        self.storage_path = Path(storage_path)
        self.storage_path.mkdir(exist_ok=True)
        self.update_count = 0
        self.profile_cache = {}
        
    def update_profile(self,
                      user_id: int,
                      attempt_data: Dict[str, Any],
                      challenge_data: Dict[str, Any]) -> Dict[str, Any]:
        """
        Update learner profile based on new attempt data.
        
        Args:
            user_id: User identifier
            attempt_data: Data from the latest attempt
            challenge_data: Information about the challenge attempted
            
        Returns:
            Updated profile with changes applied
        """
        self.update_count += 1
        
        # Load existing profile or create new
        profile = self._load_profile(user_id)
        
        # Calculate updates for each dimension
        cognitive_updates = self._update_cognitive_profile(
            profile.get('cognitive', {}),
            attempt_data,
            challenge_data
        )
        
        behavioral_updates = self._update_behavioral_profile(
            profile.get('behavioral', {}),
            attempt_data,
            challenge_data
        )
        
        motivational_updates = self._update_motivational_profile(
            profile.get('motivational', {}),
            attempt_data,
            challenge_data
        )
        
        # Apply updates
        profile['cognitive'].update(cognitive_updates)
        profile['behavioral'].update(behavioral_updates)
        profile['motivational'].update(motivational_updates)
        
        # Update metadata
        profile['last_updated'] = datetime.now().isoformat()
        profile['total_attempts'] = profile.get('total_attempts', 0) + 1
        
        # Calculate overall performance
        profile['overall_performance'] = self._calculate_overall_performance(profile)
        
        # Determine if clustering update is needed
        requires_clustering = (
            profile['total_attempts'] % 10 == 0 or  # Every 10 attempts
            abs(profile['overall_performance'] - 
                profile.get('previous_performance', 0)) > 20  # Significant change
        )
        
        profile['previous_performance'] = profile['overall_performance']
        
        # Save updated profile
        self._save_profile(user_id, profile)
        
        return {
            'profile': profile,
            'updates': {
                'cognitive': cognitive_updates,
                'behavioral': behavioral_updates,
                'motivational': motivational_updates
            },
            'message': self._generate_update_message(profile, attempt_data),
            'requires_clustering': requires_clustering
        }
    
    def _load_profile(self, user_id: int) -> Dict[str, Any]:
        """Load existing profile or create new one."""
        # Check cache first
        if user_id in self.profile_cache:
            return self.profile_cache[user_id]
        
        profile_path = self.storage_path / f"profile_{user_id}.json"
        
        if profile_path.exists():
            with open(profile_path, 'r') as f:
                profile = json.load(f)
        else:
            # Create new profile with default values
            profile = self._create_default_profile(user_id)
        
        self.profile_cache[user_id] = profile
        return profile
    
    def _save_profile(self, user_id: int, profile: Dict[str, Any]) -> None:
        """Save profile to storage."""
        profile_path = self.storage_path / f"profile_{user_id}.json"
        
        with open(profile_path, 'w') as f:
            json.dump(profile, f, indent=2)
        
        # Update cache
        self.profile_cache[user_id] = profile
    
    def _create_default_profile(self, user_id: int) -> Dict[str, Any]:
        """Create a new profile with default values."""
        return {
            'user_id': user_id,
            'created_at': datetime.now().isoformat(),
            'last_updated': datetime.now().isoformat(),
            'total_attempts': 0,
            'overall_performance': 50.0,
            
            'cognitive': {
                'problem_solving_score': 50.0,
                'logical_reasoning_score': 50.0,
                'pattern_recognition_score': 50.0,
                'abstraction_score': 50.0,
                'competency_scores': {},
                'strongest_area': None,
                'weakest_area': None
            },
            
            'behavioral': {
                'total_attempts': 0,
                'successful_attempts': 0,
                'average_time_per_challenge': 0.0,
                'hints_used': 0,
                'preferred_challenge_types': [],
                'learning_pace': 'moderate',
                'persistence_level': 'medium',
                'error_patterns': []
            },
            
            'motivational': {
                'engagement_level': 50.0,
                'persistence_score': 50.0,
                'streak_days': 0,
                'last_active_date': datetime.now().isoformat(),
                'achievements': [],
                'motivation_trend': 'stable'
            }
        }
    
    def _update_cognitive_profile(self,
                                 cognitive: Dict[str, Any],
                                 attempt_data: Dict[str, Any],
                                 challenge_data: Dict[str, Any]) -> Dict[str, Any]:
        """Update cognitive dimensions based on attempt performance."""
        updates = {}
        
        score = attempt_data.get('score', 0)
        is_successful = attempt_data.get('is_successful', False)
        competency_id = challenge_data.get('competency_id')
        
        # Update problem-solving score (weighted average)
        old_ps = cognitive.get('problem_solving_score', 50)
        weight = 0.1  # Learning rate
        updates['problem_solving_score'] = old_ps * (1 - weight) + score * weight
        
        # Update logical reasoning based on code quality
        if 'code_quality' in attempt_data:
            quality = attempt_data['code_quality']
            if quality.get('has_recursion'):
                updates['logical_reasoning_score'] = min(100, 
                    cognitive.get('logical_reasoning_score', 50) + 2)
            if quality.get('complexity_estimate', 0) > 5:
                updates['pattern_recognition_score'] = min(100,
                    cognitive.get('pattern_recognition_score', 50) + 1)
        
        # Update competency scores
        if competency_id:
            comp_scores = cognitive.get('competency_scores', {})
            comp_scores[str(competency_id)] = comp_scores.get(str(competency_id), 50)
            
            # Exponential moving average update
            alpha = 0.2
            comp_scores[str(competency_id)] = (
                (1 - alpha) * comp_scores[str(competency_id)] + 
                alpha * score
            )
            updates['competency_scores'] = comp_scores
        
        # TODO: Integrate with dataset patterns for more sophisticated cognitive modeling
        # Can load mistake patterns from dataset.json to refine scoring
        
        return updates
    
    def _update_behavioral_profile(self,
                                  behavioral: Dict[str, Any],
                                  attempt_data: Dict[str, Any],
                                  challenge_data: Dict[str, Any]) -> Dict[str, Any]:
        """Update behavioral patterns based on interaction data."""
        updates = {}
        
        # Update attempt counts
        updates['total_attempts'] = behavioral.get('total_attempts', 0) + 1
        
        if attempt_data.get('is_successful'):
            updates['successful_attempts'] = behavioral.get('successful_attempts', 0) + 1
        
        # Update time metrics
        time_spent = attempt_data.get('time_spent', 0)
        if time_spent > 0:
            old_avg = behavioral.get('average_time_per_challenge', 0)
            n = behavioral.get('total_attempts', 0)
            # Incremental average update
            updates['average_time_per_challenge'] = (old_avg * n + time_spent) / (n + 1)
        
        # Track hint usage
        if attempt_data.get('hints_used', 0) > 0:
            updates['hints_used'] = behavioral.get('hints_used', 0) + attempt_data['hints_used']
        
        # Determine learning pace based on time and success rate
        if updates.get('total_attempts', 0) > 5:
            success_rate = updates.get('successful_attempts', 0) / updates['total_attempts']
            avg_time = updates.get('average_time_per_challenge', 0)
            
            if success_rate > 0.7 and avg_time < 300:  # Fast learner
                updates['learning_pace'] = 'fast'
            elif success_rate < 0.3 or avg_time > 900:  # Slow learner
                updates['learning_pace'] = 'slow'
            else:
                updates['learning_pace'] = 'moderate'
        
        # Track error patterns
        if 'error_type' in attempt_data and attempt_data['error_type']:
            error_patterns = behavioral.get('error_patterns', [])
            error_patterns.append({
                'type': attempt_data['error_type'],
                'timestamp': datetime.now().isoformat()
            })
            # Keep only last 20 errors
            updates['error_patterns'] = error_patterns[-20:]
        
        # TODO: Add clustering features from dataset analysis
        
        return updates
    
    def _update_motivational_profile(self,
                                    motivational: Dict[str, Any],
                                    attempt_data: Dict[str, Any],
                                    challenge_data: Dict[str, Any]) -> Dict[str, Any]:
        """Update motivational indicators based on engagement patterns."""
        updates = {}
        
        # Update engagement level based on frequency and success
        last_active = motivational.get('last_active_date')
        if last_active:
            last_date = datetime.fromisoformat(last_active)
            days_since = (datetime.now() - last_date).days
            
            if days_since == 0:  # Same day
                updates['engagement_level'] = min(100, 
                    motivational.get('engagement_level', 50) + 2)
            elif days_since == 1:  # Consecutive day
                updates['streak_days'] = motivational.get('streak_days', 0) + 1
                updates['engagement_level'] = min(100,
                    motivational.get('engagement_level', 50) + 5)
            else:  # Break in streak
                updates['streak_days'] = 1
                updates['engagement_level'] = max(0,
                    motivational.get('engagement_level', 50) - days_since)
        
        updates['last_active_date'] = datetime.now().isoformat()
        
        # Update persistence score based on retry patterns
        if not attempt_data.get('is_successful'):
            # Check if student retries after failure
            updates['persistence_score'] = min(100,
                motivational.get('persistence_score', 50) + 1)
        
        # Achievement tracking
        achievements = motivational.get('achievements', [])
        
        # Check for new achievements
        if updates.get('streak_days', 0) >= 7 and 'week_streak' not in achievements:
            achievements.append('week_streak')
            
        if attempt_data.get('score', 0) == 100 and 'perfect_score' not in achievements:
            achievements.append('perfect_score')
            
        updates['achievements'] = achievements
        
        # Determine motivation trend
        current_engagement = updates.get('engagement_level', 
                                        motivational.get('engagement_level', 50))
        if current_engagement > 70:
            updates['motivation_trend'] = 'increasing'
        elif current_engagement < 30:
            updates['motivation_trend'] = 'decreasing'
        else:
            updates['motivation_trend'] = 'stable'
        
        return updates
    
    def _calculate_overall_performance(self, profile: Dict[str, Any]) -> float:
        """Calculate overall performance score from all dimensions."""
        cognitive_score = np.mean([
            profile['cognitive'].get('problem_solving_score', 50),
            profile['cognitive'].get('logical_reasoning_score', 50),
            profile['cognitive'].get('pattern_recognition_score', 50),
            profile['cognitive'].get('abstraction_score', 50)
        ])
        
        behavioral_score = 50.0
        if profile['behavioral'].get('total_attempts', 0) > 0:
            success_rate = (profile['behavioral'].get('successful_attempts', 0) / 
                          profile['behavioral']['total_attempts'])
            behavioral_score = success_rate * 100
        
        motivational_score = (profile['motivational'].get('engagement_level', 50) + 
                            profile['motivational'].get('persistence_score', 50)) / 2
        
        # Weighted average
        weights = [0.5, 0.3, 0.2]  # Cognitive, Behavioral, Motivational
        overall = (cognitive_score * weights[0] + 
                  behavioral_score * weights[1] + 
                  motivational_score * weights[2])
        
        return round(overall, 2)
    
    def _generate_update_message(self, 
                                profile: Dict[str, Any], 
                                attempt_data: Dict[str, Any]) -> str:
        """Generate personalized feedback message based on profile update."""
        messages = []
        
        if attempt_data.get('is_successful'):
            messages.append("Great job! Your solution was successful.")
        else:
            messages.append("Keep trying! Each attempt helps you learn.")
        
        # Add personalized insights
        if profile['motivational'].get('streak_days', 0) > 3:
            messages.append(f"You're on a {profile['motivational']['streak_days']}-day streak!")
        
        if profile['behavioral'].get('learning_pace') == 'fast':
            messages.append("You're making rapid progress!")
        elif profile['behavioral'].get('learning_pace') == 'slow':
            messages.append("Take your time - understanding is more important than speed.")
        
        return " ".join(messages)
    
    def get_stats(self) -> Dict[str, Any]:
        """Get profile manager statistics."""
        return {
            "profiles_updated": self.update_count,
            "cached_profiles": len(self.profile_cache),
            "storage_path": str(self.storage_path)
        }
    
    def health_check(self) -> bool:
        """Check if profile manager is healthy."""
        try:
            # Test profile creation and saving
            test_profile = self._create_default_profile(-1)
            return 'user_id' in test_profile and 'cognitive' in test_profile
        except:
            return False