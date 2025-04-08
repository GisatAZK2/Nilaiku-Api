from flask import Flask, request, jsonify
import numpy as np
import pickle
import pandas as pd

app = Flask(__name__)

# Muat model terbaik dari BayesSearchCV
with open('nilaiku_xgb_tuned_model.pkl', 'rb') as file:
    model_wrapper = pickle.load(file)
    # Extract the best estimator from the BayesSearchCV object
    if hasattr(model_wrapper, 'best_estimator_'):
        best_model = model_wrapper.best_estimator_
    else:
        best_model = model_wrapper

@app.route('/predict', methods=['POST'])
def predict():
    try:
        data = request.json

        # Konversi data ke format model
        input_features = [
            float(data['attendance']),
            float(data['hours_studied']),
            float(data['previous_scores']),
            float(data['sleep_hours']),
            float(data['tutoring_sessions']),
            float(data['peer_influence']),
            float(data['motivation_level']),
            float(data['teacher_quality']),
            float(data['access_to_resources'])
        ]

        # Konversi ke DataFrame dengan nama kolom yang sesuai
        column_names = ['Attendance', 'Hours_Studied', 'Previous_Scores', 'Sleep_Hours',
                        'Tutoring_Sessions', 'Peer_Influence', 'Motivation_Level',
                        'Teacher_Quality', 'Access_to_Resources']

        # Create DataFrame with correct shape (1 row, multiple columns)
        new_data = pd.DataFrame([input_features], columns=column_names)

        # Melakukan prediksi dengan model terbaik
        predicted_score = best_model.predict(new_data)[0]

        return jsonify({
            'predicted_score': float(predicted_score)
        })

    except Exception as e:
        return jsonify({'error': str(e), 'trace': str(traceback.format_exc())}), 500

if __name__ == '__main__':
    import traceback
    app.run(host='0.0.0.0', port=5001)