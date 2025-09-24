#!/usr/bin/env python3
"""
Code Evaluator Module
=====================
Secure code execution and test validation service.

This module provides sandboxed code execution with timeout protection,
memory monitoring, and comprehensive test result analysis.
"""

import ast
import sys
import json
import time
import traceback
import resource
import signal
from typing import Dict, List, Any, Tuple, Optional
from io import StringIO
from contextlib import contextmanager
import logging


if sys.platform != "win32":
    import resource
else:
    resource = None

logger = logging.getLogger(__name__)


class CodeExecutionError(Exception):
    """Raised when code execution fails."""
    pass


class CodeEvaluator:
    """
    Secure code evaluation service with sandbox capabilities.
    
    In production, this should use Docker containers or similar
    isolation mechanisms for true sandboxing.
    """
    
    def __init__(self):
        self.evaluation_count = 0
        self.max_execution_time = 5  # seconds
        self.max_memory = 50 * 1024 * 1024  # 50 MB
        
    def evaluate(self, 
                 code: str, 
                 test_cases: List[Dict[str, Any]], 
                 language: str = "python",
                 timeout: int = 5) -> Dict[str, Any]:
        """
        Evaluate code against test cases with security measures.
        
        Args:
            code: Student's submitted code
            test_cases: List of test cases with input/output
            language: Programming language (currently only Python)
            timeout: Execution timeout in seconds
            
        Returns:
            Evaluation results including test outcomes and metrics
        """
        self.evaluation_count += 1
        
        if language != "python":
            return {
                "success": False,
                "error": f"Language {language} not supported yet",
                "test_results": []
            }
        
        # Validate code syntax first
        syntax_valid, syntax_error = self._validate_syntax(code)
        if not syntax_valid:
            return {
                "success": False,
                "error": f"Syntax error: {syntax_error}",
                "test_results": [],
                "code_quality": {"syntax_valid": False}
            }
        
        # Extract function name
        func_name = self._extract_function_name(code)
        if not func_name:
            return {
                "success": False,
                "error": "No function definition found",
                "test_results": []
            }
        
        # Run tests
        test_results = []
        execution_times = []
        
        for i, test_case in enumerate(test_cases):
            try:
                result = self._run_single_test(
                    code, func_name, test_case, timeout
                )
                test_results.append(result)
                if result.get('execution_time'):
                    execution_times.append(result['execution_time'])
                    
            except Exception as e:
                logger.error(f"Test {i} failed: {str(e)}")
                test_results.append({
                    "test_id": i,
                    "passed": False,
                    "error": str(e),
                    "input": test_case.get('input'),
                    "expected": test_case.get('output')
                })
        
        # Calculate metrics
        passed_count = sum(1 for r in test_results if r.get('passed', False))
        all_passed = passed_count == len(test_cases)
        
        # Code quality analysis
        code_quality = self._analyze_code_quality(code)
        
        return {
            "success": all_passed,
            "test_results": test_results,
            "execution_time": sum(execution_times) if execution_times else None,
            "memory_used": None,  # Placeholder for memory tracking
            "error": None if all_passed else "Some tests failed",
            "code_quality": code_quality
        }
    
    def _validate_syntax(self, code: str) -> Tuple[bool, Optional[str]]:
        """Validate Python syntax."""
        try:
            ast.parse(code)
            return True, None
        except SyntaxError as e:
            return False, str(e)
    
    def _extract_function_name(self, code: str) -> Optional[str]:
        """Extract the first function name from code."""
        try:
            tree = ast.parse(code)
            for node in ast.walk(tree):
                if isinstance(node, ast.FunctionDef):
                    return node.name
            return None
        except:
            return None
    
    def _run_single_test(self, 
                        code: str, 
                        func_name: str, 
                        test_case: Dict[str, Any],
                        timeout: int) -> Dict[str, Any]:
        """
        Run a single test case in a semi-isolated environment.
        
        WARNING: This is a simplified sandbox. In production, use:
        - Docker containers
        - Virtual machines
        - Proper sandboxing libraries (e.g., pysandbox, RestrictedPython)
        """
        start_time = time.time()
        
        # Create isolated namespace
        namespace = {
            '__builtins__': {
                # Whitelist safe built-in functions
                'len': len,
                'range': range,
                'enumerate': enumerate,
                'int': int,
                'str': str,
                'float': float,
                'list': list,
                'dict': dict,
                'set': set,
                'tuple': tuple,
                'min': min,
                'max': max,
                'sum': sum,
                'sorted': sorted,
                'reversed': reversed,
                'isinstance': isinstance,
                'print': print,  # Captured below
                'True': True,
                'False': False,
                'None': None,
            }
        }
        
        # Capture stdout
        old_stdout = sys.stdout
        stdout_capture = StringIO()
        
        try:
            sys.stdout = stdout_capture
            
            # Execute code in namespace
            exec(code, namespace)
            
            # Get the function
            if func_name not in namespace:
                raise CodeExecutionError(f"Function {func_name} not found")
            
            func = namespace[func_name]
            
            # Prepare input
            test_input = test_case.get('input', {})
            expected_output = test_case.get('output')
            
            # Call function with timeout protection
            # Note: Simplified timeout - use signal.alarm() or threading for real timeout
            if isinstance(test_input, dict):
                actual_output = func(**test_input)
            elif isinstance(test_input, list):
                actual_output = func(*test_input)
            else:
                actual_output = func(test_input)
            
            execution_time = time.time() - start_time
            
            # Compare outputs
            passed = actual_output == expected_output
            
            return {
                "test_id": test_case.get('id', 0),
                "passed": passed,
                "input": test_input,
                "expected": expected_output,
                "actual": actual_output,
                "execution_time": execution_time,
                "stdout": stdout_capture.getvalue()
            }
            
        except Exception as e:
            return {
                "test_id": test_case.get('id', 0),
                "passed": False,
                "input": test_input,
                "expected": expected_output,
                "actual": None,
                "error": str(e),
                "error_type": type(e).__name__,
                "traceback": traceback.format_exc()
            }
            
        finally:
            sys.stdout = old_stdout
    
    def _analyze_code_quality(self, code: str) -> Dict[str, Any]:
        """Analyze code quality metrics."""
        try:
            tree = ast.parse(code)
            
            # Count various code elements
            functions = sum(1 for n in ast.walk(tree) if isinstance(n, ast.FunctionDef))
            loops = sum(1 for n in ast.walk(tree) 
                       if isinstance(n, (ast.For, ast.While)))
            conditionals = sum(1 for n in ast.walk(tree) if isinstance(n, ast.If))
            
            # Check for recursion
            has_recursion = False
            for node in ast.walk(tree):
                if isinstance(node, ast.FunctionDef):
                    func_name = node.name
                    for inner_node in ast.walk(node):
                        if isinstance(inner_node, ast.Call):
                            if (isinstance(inner_node.func, ast.Name) and 
                                inner_node.func.id == func_name):
                                has_recursion = True
                                break
            
            # Code metrics
            lines = code.count('\n') + 1
            chars = len(code)
            
            # Check for common patterns
            has_base_case = 'if' in code and ('return' in code or 'break' in code)
            uses_builtin = any(builtin in code 
                             for builtin in ['sorted', 'min', 'max', 'sum', 'len'])
            
            return {
                "syntax_valid": True,
                "lines_of_code": lines,
                "character_count": chars,
                "function_count": functions,
                "loop_count": loops,
                "conditional_count": conditionals,
                "has_recursion": has_recursion,
                "has_base_case": has_base_case,
                "uses_builtin": uses_builtin,
                "complexity_estimate": loops + conditionals + (5 if has_recursion else 0)
            }
            
        except Exception as e:
            logger.error(f"Code quality analysis failed: {str(e)}")
            return {"syntax_valid": False, "error": str(e)}
    
    def get_stats(self) -> Dict[str, Any]:
        """Get evaluation statistics."""
        return {
            "evaluations_completed": self.evaluation_count,
            "max_execution_time": self.max_execution_time,
            "max_memory": self.max_memory
        }
    
    def health_check(self) -> bool:
        """Check if evaluator is healthy."""
        try:
            # Test with simple code
            test_code = "def test(): return 42"
            test_cases = [{"input": {}, "output": 42}]
            result = self.evaluate(test_code, test_cases)
            return result.get('success', False)
        except:
            return False


# Example usage and testing
if __name__ == "__main__":
    evaluator = CodeEvaluator()
    
    # Test case 1: Correct implementation
    code1 = """
def two_sum(nums, target):
    seen = {}
    for i, n in enumerate(nums):
        if target - n in seen:
            return [seen[target - n], i]
        seen[n] = i
    return []
"""
    
    test_cases1 = [
        {"input": {"nums": [2, 7, 11, 15], "target": 9}, "output": [0, 1]},
        {"input": {"nums": [3, 3], "target": 6}, "output": [0, 1]},
    ]
    
    print("Test 1: Correct implementation")
    result1 = evaluator.evaluate(code1, test_cases1)
    print(json.dumps(result1, indent=2))
    
    # Test case 2: Incorrect implementation (missing base case)
    code2 = """
def factorial(n):
    return n * factorial(n - 1)
"""
    
    test_cases2 = [
        {"input": {"n": 5}, "output": 120},
        {"input": {"n": 0}, "output": 1},
    ]
    
    print("\nTest 2: Missing base case")
    result2 = evaluator.evaluate(code2, test_cases2)
    print(json.dumps(result2, indent=2))