#!/usr/bin/env python3
"""
Expert Rules Engine
===================
Rule-based system for generating personalized feedback and recommendations.

Implements pedagogical rules based on Polya's problem-solving strategy
and common programming mistake patterns.
"""

import re
import ast
import json
import logging
from typing import Dict, List, Any, Optional, Tuple
from pathlib import Path
from collections import defaultdict

logger = logging.getLogger(__name__)


class ExpertRulesEngine:
    """
    Expert system for educational feedback generation.
    
    Uses rule-based approach combined with pattern matching to provide
    targeted pedagogical interventions.
    """
    
    def __init__(self, rules_path: str = "./rules"):
        self.rules_path = Path(rules_path)
        self.rules_path.mkdir(exist_ok=True)
        
        self.rules = self._load_rules()
        self.feedback_count = 0
        self.pattern_cache = {}
        
        # Common mistake patterns from research
        self.mistake_patterns = {
            'missing_base_case': {
                'indicators': ['recursion without base', 'no termination', 'infinite loop'],
                'feedback': 'Your recursive function needs a base case to prevent infinite recursion.',
                'hints': [
                    'Add a condition that stops the recursion',
                    'Check if n <= 1 or n == 0 for typical recursive problems',
                    'Ensure your base case will eventually be reached'
                ]
            },
            'off_by_one': {
                'indicators': ['index error', 'boundary', 'len()', 'range'],
                'feedback': 'Check your array indices - you might have an off-by-one error.',
                'hints': [
                    'Remember Python uses 0-based indexing',
                    'The last index is len(array) - 1, not len(array)',
                    'Be careful with loop boundaries'
                ]
            },
            'wrong_operator': {
                'indicators': ['comparison', '==', '=', 'condition'],
                'feedback': 'Review your operators - ensure you\'re using the correct one.',
                'hints': [
                    'Use == for comparison, = for assignment',
                    'Check < vs <= in your conditions',
                    'Verify AND vs OR in complex conditions'
                ]
            },
            'returns_constant': {
                'indicators': ['return 0', 'return 1', 'return true', 'hardcoded'],
                'feedback': 'Your function returns a constant value instead of computing the result.',
                'hints': [
                    'Process the input parameters to generate the result',
                    'Avoid hardcoding return values',
                    'Make sure your function logic actually runs'
                ]
            },
            'uses_builtin_incorrectly': {
                'indicators': ['sorted()', 'min()', 'max()', 'inefficient'],
                'feedback': 'Consider if built-in functions are being used appropriately.',
                'hints': [
                    'Check if the problem requires a specific algorithm',
                    'Understand the time complexity implications',
                    'Some problems expect manual implementation'
                ]
            }
        }
    
    def extract_code_features(self,
                             code: str,
                             test_results: List[Dict[str, Any]],
                             error_message: Optional[str] = None) -> Dict[str, Any]:
        """
        Extract features from code for rule matching.
        
        Args:
            code: Student's submitted code
            test_results: Results from test execution
            error_message: Any error message from execution
            
        Returns:
            Dictionary of extracted features
        """
        features = {
            'code_length': len(code),
            'line_count': code.count('\n') + 1,
            'has_functions': 'def ' in code,
            'has_loops': 'for ' in code or 'while ' in code,
            'has_conditionals': 'if ' in code,
            'has_recursion': False,
            'has_base_case': False,
            'uses_builtins': [],
            'error_type': None,
            'failing_tests': 0,
            'success_rate': 0.0,
            'detected_patterns': []
        }
        
        # Analyze code structure
        try:
            tree = ast.parse(code)
            
            # Check for recursion
            for node in ast.walk(tree):
                if isinstance(node, ast.FunctionDef):
                    func_name = node.name
                    for inner_node in ast.walk(node):
                        if isinstance(inner_node, ast.Call):
                            if (isinstance(inner_node.func, ast.Name) and
                                inner_node.func.id == func_name):
                                features['has_recursion'] = True
                                break
            
            # Check for base case in recursive functions
            if features['has_recursion']:
                features['has_base_case'] = 'return' in code and ('if' in code or 'elif' in code)
            
        except SyntaxError:
            features['error_type'] = 'syntax_error'
        
        # Analyze test results
        if test_results:
            failing = sum(1 for t in test_results if not t.get('passed', False))
            features['failing_tests'] = failing
            features['success_rate'] = 1.0 - (failing / len(test_results))
        
        # Check for built-in functions
        builtins_used = []
        for builtin in ['sorted', 'min', 'max', 'sum', 'len', 'enumerate', 'range']:
            if builtin + '(' in code:
                builtins_used.append(builtin)
        features['uses_builtins'] = builtins_used
        
        # Detect mistake patterns
        detected = []
        for pattern_name, pattern_info in self.mistake_patterns.items():
            if self._matches_pattern(code, error_message, pattern_info['indicators']):
                detected.append(pattern_name)
        features['detected_patterns'] = detected
        
        return features
    
    def generate_feedback(self,
                         features: Dict[str, Any],
                         user_profile: Optional[Dict[str, Any]] = None) -> Dict[str, Any]:
        """
        Generate personalized feedback based on code features and user profile.
        
        Args:
            features: Extracted code features
            user_profile: Optional user learning profile
            
        Returns:
            Comprehensive feedback dictionary
        """
        self.feedback_count += 1
        
        feedback = {
            'primary_feedback': {},
            'hints': [],
            'resources': [],
            'next_steps': [],
            'confidence': 0.8,
            'polya_guidance': {}
        }
        
        # Apply mistake pattern rules
        if features['detected_patterns']:
            primary_pattern = features['detected_patterns'][0]
            pattern_info = self.mistake_patterns.get(primary_pattern, {})
            
            feedback['primary_feedback'] = {
                'category': primary_pattern,
                'message': pattern_info.get('feedback', 'Review your code for common mistakes.'),
                'severity': 'high' if features['failing_tests'] > 2 else 'medium'
            }
            feedback['hints'] = pattern_info.get('hints', [])
        
        # Apply Polya strategy guidance
        polya_guidance = self._generate_polya_guidance(features, user_profile)
        feedback['polya_guidance'] = polya_guidance
        
        # Syntax error handling
        if features.get('error_type') == 'syntax_error':
            feedback['primary_feedback'] = {
                'category': 'syntax_error',
                'message': 'Your code has syntax errors. Check for missing colons, parentheses, or indentation.',
                'severity': 'critical'
            }
            feedback['hints'] = [
                'Check that all parentheses and brackets are balanced',
                'Verify proper indentation (use 4 spaces)',
                'Ensure colons after if/for/while/def statements'
            ]
        
        # Performance-based feedback
        if features['success_rate'] == 1.0:
            feedback['next_steps'] = [
                'Try optimizing your solution for better performance',
                'Consider edge cases that weren\'t tested',
                'Move on to more challenging problems'
            ]
        elif features['success_rate'] < 0.5:
            feedback['next_steps'] = [
                'Review the problem statement carefully',
                'Try solving a simpler version first',
                'Use print statements to debug your logic'
            ]
        
        # Add resources based on patterns
        feedback['resources'] = self._get_learning_resources(features['detected_patterns'])
        
        # Adjust confidence based on pattern clarity
        if len(features['detected_patterns']) > 0:
            feedback['confidence'] = 0.9
        elif features['error_type']:
            feedback['confidence'] = 0.85
        else:
            feedback['confidence'] = 0.6
        
        # Personalize based on user profile
        if user_profile:
            feedback = self._personalize_feedback(feedback, user_profile)
        
        return feedback
    
    def _matches_pattern(self, 
                        code: str, 
                        error_msg: Optional[str],
                        indicators: List[str]) -> bool:
        """Check if code matches a mistake pattern."""
        code_lower = code.lower()
        error_lower = (error_msg or '').lower()
        
        for indicator in indicators:
            if indicator.lower() in code_lower or indicator.lower() in error_lower:
                return True
        return False
    
    def _generate_polya_guidance(self,
                                features: Dict[str, Any],
                                profile: Optional[Dict[str, Any]] = None) -> Dict[str, str]:
        """Generate guidance based on Polya's problem-solving strategy."""
        guidance = {}
        
        # Understanding phase
        if features['success_rate'] < 0.3:
            guidance['understand'] = (
                "Make sure you fully understand what the problem is asking. "
                "Try explaining it in your own words or working through an example by hand."
            )
        
        # Planning phase
        if not features['has_functions'] and features['line_count'] > 20:
            guidance['plan'] = (
                "Consider breaking your solution into functions. "
                "Plan your approach before coding - what are the main steps?"
            )
        
        # Implementation phase
        if features['detected_patterns']:
            guidance['implement'] = (
                "Focus on fixing the identified issues in your implementation. "
                "Test each part of your code independently."
            )
        
        # Verification phase
        if features['failing_tests'] > 0:
            guidance['verify'] = (
                f"Your solution fails {features['failing_tests']} test(s). "
                "Add print statements to see what your code is actually doing."
            )
        
        return guidance
    
    def _get_learning_resources(self, patterns: List[str]) -> List[Dict[str, str]]:
        """Get relevant learning resources based on detected patterns."""
        resources = []
        
        resource_map = {
            'missing_base_case': {
                'title': 'Understanding Recursion and Base Cases',
                'type': 'article',
                'url': '#recursion-guide'
            },
            'off_by_one': {
                'title': 'Common Array Indexing Mistakes',
                'type': 'tutorial',
                'url': '#indexing-guide'
            },
            'wrong_operator': {
                'title': 'Python Operators Reference',
                'type': 'reference',
                'url': '#operators-ref'
            },
            'returns_constant': {
                'title': 'Dynamic vs Static Return Values',
                'type': 'concept',
                'url': '#dynamic-returns'
            },
            'uses_builtin_incorrectly': {
                'title': 'When to Use Built-in Functions',
                'type': 'best-practice',
                'url': '#builtin-guide'
            }
        }
        
        for pattern in patterns[:3]:  # Limit to top 3 resources
            if pattern in resource_map:
                resources.append(resource_map[pattern])
        
        # Add general resource if no specific patterns
        if not resources:
            resources.append({
                'title': 'General Problem-Solving Strategies',
                'type': 'guide',
                'url': '#problem-solving'
            })
        
        return resources
    
    def _personalize_feedback(self,
                             feedback: Dict[str, Any],
                             profile: Dict[str, Any]) -> Dict[str, Any]:
        """Personalize feedback based on user profile."""
        # Adjust based on cognitive level
        cognitive = profile.get('cognitive', {}).get('problem_solving_score', 50)
        
        if cognitive < 40:
            # Simplify for struggling students
            feedback['hints'] = feedback['hints'][:2]  # Fewer hints
            feedback['primary_feedback']['message'] = (
                feedback['primary_feedback'].get('message', '') + 
                " Let's take it step by step."
            )
        elif cognitive > 70:
            # Challenge advanced students
            feedback['next_steps'].append('Can you solve this with a different approach?')
        
        # Adjust based on motivation
        motivation = profile.get('motivational', {}).get('engagement_level', 50)
        
        if motivation < 30:
            feedback['encouragement'] = (
                "Don't give up! Every attempt teaches you something new. "
                "You're making progress even when it doesn't feel like it."
            )
        elif motivation > 70:
            feedback['encouragement'] = (
                "Excellent persistence! You're well on your way to mastering this."
            )
        
        # Adjust based on learning pace
        pace = profile.get('behavioral', {}).get('learning_pace', 'moderate')
        
        if pace == 'slow':
            feedback['pacing_advice'] = (
                "Take your time to understand each concept thoroughly. "
                "Quality understanding is more important than speed."
            )
        elif pace == 'fast':
            feedback['pacing_advice'] = (
                "You're moving quickly! Make sure to consolidate your understanding "
                "with practice problems."
            )
        
        return feedback
    
    def _load_rules(self) -> Dict[str, Any]:
        """Load expert rules from configuration."""
        rules_file = self.rules_path / "expert_rules.json"
        
        if rules_file.exists():
            try:
                with open(rules_file, 'r') as f:
                    return json.load(f)
            except Exception as e:
                logger.error(f"Failed to load rules: {str(e)}")
        
        # Return default rules
        return {
            'syntax_rules': {
                'missing_colon': {
                    'pattern': r'(if|while|for|def|class)\s+[^:]+$',
                    'message': 'Missing colon after control statement'
                }
            },
            'logic_rules': {
                'infinite_loop': {
                    'pattern': r'while\s+True|while\s+1',
                    'message': 'Potential infinite loop detected'
                }
            }
        }
    
    def apply_custom_rules(self, code: str) -> List[Dict[str, str]]:
        """
        Apply custom expert rules for additional insights.
        
        TODO: Extend with domain-specific rules from research
        """
        insights = []
        
        # Check for common antipatterns
        if 'global ' in code:
            insights.append({
                'type': 'style',
                'message': 'Avoid using global variables when possible'
            })
        
        if code.count('try:') > 3:
            insights.append({
                'type': 'design',
                'message': 'Too many try blocks might indicate design issues'
            })
        
        # Check for good practices
        if 'def ' in code and '"""' in code:
            insights.append({
                'type': 'positive',
                'message': 'Good job documenting your functions!'
            })
        
        return insights
    
    def get_stats(self) -> Dict[str, Any]:
        """Get expert rules engine statistics."""
        return {
            "feedback_generated": self.feedback_count,
            "rules_loaded": len(self.rules),
            "patterns_defined": len(self.mistake_patterns),
            "cache_size": len(self.pattern_cache)
        }
    
    def health_check(self) -> bool:
        """Check if expert rules engine is healthy."""
        try:
            # Test with sample code
            test_code = "def factorial(n):\n    return n * factorial(n-1)"
            features = self.extract_code_features(test_code, [], None)
            feedback = self.generate_feedback(features)
            return 'primary_feedback' in feedback and 'hints' in feedback
        except:
            return False