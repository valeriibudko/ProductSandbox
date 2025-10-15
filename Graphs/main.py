import matplotlib.pyplot as plt
import numpy as np
import pandas as pd

df = pd.read_csv("student_exam_scores.csv")

# print(df["exam_score"].value_counts())

exam_score_count = df["exam_score"].value_counts()

plt.bar(exam_score_count.index, exam_score_count.values)
plt.xlabel("Exam score")
plt.ylabel("Count")
plt.show()
